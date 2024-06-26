function spider(id, nb) {
    g = new Graphique(id, "radar")
    g.createXAxis("var")
    g.createYAxis(null,{max:100,min:0})
    g.setDataXAxis([{"var":"PIB/Hab"},{"var":"% énergies ren."},{"var":"Emissions de CO2"},{"var":"Arrivées touristiques"},{"var":"Départs"},{"var":"Global Peace Index"},{"var":"IDH"}])
    g.addSlider(updateSpider,400,-20,50,50,90,1995,2022)

    for (i=0;i<nb;i++) {
        g.addSerie("radar", "var", "value", null, "{name} : {valueY}", color[i])
    }
}

var color = ["#52796F", "#83A88B"];
function spiderHTMX(index, data, dataComp, name) {
    g.updateSerie(index, data, name, dataComp);
    updateTable(index, dataComp[g.getYear()]);

    if (g.getSeriesLength() == 1) {
        updateGrow(dataComp, g.getYear())
    } else if (g.getSeriesLength() == 2) {
        updateBold(g.getSeries(), g.getYear())
    }
}

function updateSpider(year) {
    for (var s of g.getSeries()) {
        s.setDataSerie(s.data[year]);
        updateTable(s.getIndex(), s.comp[year]);
    }

    if (g.getSeriesLength() == 1) {
        updateGrow(g.getSeries()[0].comp, year)
    } else if (g.getSeriesLength() == 2) {
        updateBold(g.getSeries(),year)
    }
}

function updateTable(index, data) {
    if (data) {      
        for (var i=0;i<data.length;i++) {
            st = formatNumber(data[i]["value"], data[i]["var"])
            $("#td_"+data[i]["var"]+"_"+index).html(st)
        }
    }
}

function formatNumber(value, variable) {
    if (value == null) {
        return "/"
    }
    if (variable == "%" || variable == "elecRenew") {
        return value.toFixed(2)+" %"
    } 
    if (variable == "gpi" || variable == "idh") {
        return value
    }

    if (value > Math.pow(10,9)) {
        st = (value/Math.pow(10,9)).toFixed(2)+"Ma"
    } else if (value > Math.pow(10,6)) {
        st = (value/Math.pow(10,6)).toFixed(2)+"M"
    } else if (value > Math.pow(10,3)) {
        st = value.toFixed(0).toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, " ")
    } else {
        st = value.toFixed(0)
    }

    if (variable == "pib" || variable == "pibParHab") {
        st += " $"
    }
    return st
}

function updateGrow(data, year) {
    for (var i=0;i<data[year].length;i++) {
        try {
            if (data[year][i]["value"] == null || data[year-1][i]["value"] == null) {
                throw new Error()
            }
            grow = (100*(data[year][i]["value"] - data[year-1][i]["value"])/data[year-1][i]["value"]).toFixed(2)
            rank = data[year][i]["rank"]
            evolRank = data[year-1][i]["rank"] - rank
            if (grow > 0) {
                iconGrow = "<img src='assets/icons/miniup.svg' class='small-icon'>"
            } else if (grow < 0) {
                iconGrow = "<img src='assets/icons/minidown.svg' class='small-icon'>"
            } else {
                grow = "-"
                iconGrow = ""
            }
            grow += "%"
            rank += "e"

            if (evolRank > 0) {
                iconRank = "<img src='assets/icons/miniup.svg' class='small-icon'>"
            } else if (evolRank < 0) {
                iconRank = "<img src='assets/icons/minidown.svg' class='small-icon'>"
            } else {
                evolRank = "-"
                iconRank = ""
            }
        } catch {
            grow = "/"
            rank = "/"
            evolRank = "/"
            iconGrow = ""
            iconRank = ""
        }

        $("#td_"+data[year][i]["var"]+"_grow").html(iconGrow+" <p>"+grow+"</p>")
        $("#td_"+data[year][i]["var"]+"_rank").html(rank)
        $("#td_"+data[year][i]["var"]+"_rankEvol").html(iconRank+" <p>"+evolRank+"</p>")
    }
}

function updateBold(series,year) {
    if (series[1].comp != null) {
        $(".bold").removeClass("bold")
        for (var i=0;i<series[0].comp[year].length;i++) {
            v = series[0].comp[year][i]["var"]
            if (series[0].comp[year][i]["value"] > series[1].comp[year][i]["value"]) {
                $("#td_"+v+"_0").addClass("bold")
            } else if (series[0].comp[year][i]["value"] < series[1].comp[year][i]["value"]) {
                $("#td_"+v+"_1").addClass("bold")
            }
        }
    } 
}