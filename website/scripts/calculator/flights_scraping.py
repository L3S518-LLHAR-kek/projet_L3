"""
Script adapted from Arthur Chukhrai's article on scraping Google Flights with Python.
The script is used to scrape Google Flights data using Playwright and Selectolax libraries.
The function get_page() did not work as expected, so it was modified.
The script is modified to be used in a production environment.
Link to the article:
https://dev.to/chukhraiartur/scrape-google-flights-with-python-4dln

En production:
- ligne 24: remplacer headless=False par headless=True
- ligne 24: remplacer slow_mo=100 par slow_mo=0 (à tester en prod, le réduire tant que possible)
- commenter ligne 34 et 35
- décommenter ligne 36 et 37
- supprimer tout time.sleep sauf ligne 57 (time.sleep(1))
"""

# IMPORTS
from playwright.sync_api import sync_playwright, Playwright
from selectolax.lexbor import LexborHTMLParser
import json
import time

def get_page(playwright:Playwright, origin:str, destination:str, departure_date, passengers:int = 1):
    browser = playwright.chromium.launch(headless=True, slow_mo=100)
    page = browser.new_page()
    page.goto("https://www.google.com/travel/flights?hl=fr&curr=EUR")
    page.get_by_role("button", name="Tout accepter").click()

    page.get_by_role("combobox", name="Modifier le type de billet. ​").locator("div").click()
    time.sleep(0.4)
    page.get_by_role("option", name="Aller simple").click()

    page.get_by_label("1 passager").click()
    page.get_by_label("Nombre de passagers adultes").get_by_text("1").click()
    page.get_by_label("Ajouter un adulte").click(click_count=passengers-1)

    page.get_by_label("D'où partez-vous ?").click()
    page.get_by_label("Autres points de départ ?").fill(origin)
    page.get_by_label("Autres points de départ ?").press("Enter")
    # page.get_by_role("combobox", name="D'où partez-vous ?").fill(origin)
    # page.get_by_role("combobox", name="D'où partez-vous ?").press("Enter")

    page.get_by_role("combobox", name="Où allez-vous ?").click()
    page.get_by_role("combobox", name="Où allez-vous ?").fill(destination)
    page.get_by_role("combobox", name="Où allez-vous ?").press("Enter")
    time.sleep(0.4)

    page.get_by_role("textbox", name="Départ").click()
    page.get_by_role("textbox", name="Départ").fill(departure_date)
    page.get_by_role("textbox", name="Départ").press("Enter")
    time.sleep(0.7)

    page.get_by_label("OK. Rechercher un aller").click()
    page.get_by_label("Rechercher", exact=True).click()
    time.sleep(1)

    locators = page.locator("[jscontroller='muK14'] .VfPpkd-RLmnJb").all()
    for locator in locators:
        locator.click()

    page.pause()
    page.screenshot(path="scripts/calculator/screen.png")

    parser = LexborHTMLParser(page.content())

    return parser

def scrape_google_flights(parser):
    data = {}
    categories = parser.root.css('.zBTtmb')
    category_results = parser.root.css('.Rk10dc')

    for category, category_result in zip(categories, category_results):
        category_data = []
        for result in category_result.css('.yR1fYc'):
            date = result.css('[jscontroller="cNtv4b"] span')
            departure_date = date[0].text()
            arrival_date = date[1].text()
            company = result.css_first('.Ir0Voe .sSHqwe').text()
            duration = result.css_first('.AdWm1c.gvkrdb').text()
            stops = result.css_first('.EfT7Ae .ogfYpf').text()
            emissions = result.css_first('.V1iAHe .AdWm1c').text()
            emission_comparison = result.css_first('.N6PNV').text()
            price = result.css_first('.U3gSDe .FpEdX span').text() if result.css_first('.U3gSDe .FpEdX span').text() else 0
            price_type = result.css_first('.U3gSDe .N872Rd').text() if result.css_first('.U3gSDe .N872Rd') else None
            service = result.css_first('.hRBhge')

            flight_data = {
                'departure_date': departure_date,
                'arrival_date': arrival_date,
                'company': company,
                'duration': duration,
                'stops': stops,
                'emissions': emissions,
                'emission_comparison': emission_comparison,
                'price': price,
                'price_type': price_type,
            }

        for result in category_result.css('.m9ravf .xOMPfb.MNvMJb'):
            trips = result.css(".c257Jb.eWArhb")
            departure_airport = trips[0].css_first(".FFbonc .dPzsIb span[dir='ltr']").text()[1:-1]
            airports = [departure_airport]
            for trip in trips:
                intermediate_airports = trip.css_first(".FFbonc .SWFQlc span[dir='ltr']").text()[1:-1]
                airports.append(intermediate_airports)
            flight_data['airports'] = airports
            if service:
                flight_data['service'] = service.text()
            
            

        category_data.append(flight_data)

        data[category.text().lower().replace(' ', '_')] = category_data
    return data

def get_best_result(data):
    try:
        best_result = data["meilleurs_vols"][0]
    except KeyError as e:
        best_result = data["tous_les_vols"][0]
    best_result = {
        'duration': best_result['duration'],
        'emissions': best_result['emissions'],
        'price': best_result['price'],
        'stops': best_result['stops'],
        'airports': best_result['airports']
    }
    return best_result
    
def search(playwright, origin, destination, departure_date, passengers):
    parser = get_page(playwright, origin, destination, departure_date, passengers)
    google_flights_results = scrape_google_flights(parser)
    best_result = get_best_result(google_flights_results)
    return best_result

def run(playwright, origin, destination, departure_date, passengers):
    result = search(playwright, origin, destination, departure_date, passengers)
    result_json = json.dumps(result, ensure_ascii=False)
    print(result_json)

if __name__ == "__main__":
    passengers = 7
    origin = 'Lisbonne'
    destination = 'Paris'
    departure_date = '2024-06-05'

    with sync_playwright() as playwright:
        run(playwright, origin, destination, departure_date, passengers)