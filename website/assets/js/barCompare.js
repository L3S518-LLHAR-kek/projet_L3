var year;

function graphBar() {

    b = new Bar("bar")
    b.initXAxis("var", [{"var":"pib"},{"var":"co2"},{"var":"arrivees"},{"var":"gpi"},{"var":"cpi"}])
    b.initYAxis()
    b.addLegend()

    // Create controls
    var container = b.graph.children.push(am5.Container.new(b.root, {
        centerX: am5.p0,
        centerY: am5.p50,
        width: 700,
        layout: b.root.horizontalLayout,
        paddingTop: 10,
        paddingRight:50,
        paddingLeft:50,
        rotation:0
    }));

    var firstYear = 2008
    var lastYear = 2020

    var slider = container.children.push(am5.Slider.new(b.root, {
        orientation: "horizontal",
        start: 1,
        centerX: am5.p50,
    }));

    slider.get("background").setAll(
        {fill:"#52796F"}
    )

    slider.startGrip.get("icon").set("forceHidden", true);
    slider.startGrip.set("label", am5.Label.new(b.root, {
        text: firstYear + "",
        paddingTop: 0,
        paddingRight: 0,
        paddingBottom: 0,
        paddingLeft: 0,
        fill:"#000000",
        rotation:0
    }));

    // updateData(2020);
    var yearTemp = 2020
    year = 2020

    slider.events.on("rangechanged", function () {
        year = firstYear + Math.round(slider.get("start", 0) * (lastYear - firstYear));
        slider.startGrip.get("label").set("text", year + "");
        if (year != yearTemp) {
            updateData(year);
            yearTemp = year
        }
    });

    function updateData(year) {
        for (var s of b.series) {
            s.serie.data.setAll(s.data[year]);
        }
            
    }

    b.graph.appear(1000, 100);
    

}

var color = ["#52796F","#83A88B"]
function BarAjax(incr,data,name) {
    console.log(data[year], year, name)
    if (b.series.length == incr) {
        b.addSerie(data, name, color[incr], "var", "value")
        b.series[incr].serie.data.setAll(data[year]);
    } else {
        b.series[incr].data = data
        b.series[incr].serie.data.setAll(data[year]);
        b.series[incr].serie.setAll({
            name:name
        })
    }
}