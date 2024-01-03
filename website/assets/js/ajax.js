function compareAjax(incr, id_pays) {
    $.ajax({
        method:"GET",
        url:"ajax.php",
        data:{id_pays:id_pays},
        async:false,
        success:function(result) {
            // console.log(result)
            container = $("#bandeau"+incr)
            if (container.length) {
                $(container).remove()
            }
            createBandeau(result["id_pays"],incr,result["nom"],result["capitale"])

            result["spider"] = JSON.parse(result["spider"])
            for (var i=2008;i<2021;i++) {
                for (var j=0;j<result["spider"][i].length;j++) {
                    result["spider"][i][j]["value"] = parseFloat(result["spider"][i][j]["value"])
                }      
            }
            
            // console.log(JSON.parse(result["spider"]))
            spiderAjax(incr, result["spider"], result["nom"])

            result["line"] = JSON.parse(result["line"])
            for (var i=0;i<result["line"].length;i++) {
                for (var categ of ["co2","Enr","gpi","cpi","pib","arrivees","departs"]) {
                    if (result["line"][i][categ] == "null") {
                        result["line"][i][categ] = null
                    } else {
                        result["line"][i][categ] = parseFloat(result["line"][i][categ])
                    }
                }
            }
            lineAjax(incr, result["line"], result["nom"])

            result["bar"] = JSON.parse(result["bar"])
            console.log(result["bar"])
            for (var i=2008;i<2021;i++) {
                console.log(i)
                for (var j=0;j<result["bar"][i].length;j++) {
                    result["bar"][i][j]["value"] = parseFloat(result["bar"][i][j]["value"])
                }      
            }
            
            BarAjax(incr, result["bar"], result["nom"])
        },
        error:function(mess){
            console.log(mess)
        }
    })    
}


function createBandeau(id,nb,nom,capitale) {
    $("#bandeau").append(`
        <div class="bandeau-container" id="bandeau${nb}">     
        <img class="img" src='../assets/img/${id}.jpg' alt="Bandeau">
        <img class="flag" src='../assets/twemoji/${id}.svg'>
        <h1 class="nom">${nom}</h1>
        <p class="capital">Capitale : ${capitale}</p>
    `)
}

$(document).ready(function() {
    $("#pays0").on("change", function() {
        incr = 0
        p1 = countrySeries.getDataItemById($(this).val())
        p1._settings.mapPolygon.set("active",true)
        compareAjax("0", $(this).val(), $("#pays1").val())
    })
    
    $("#pays1").on("change", function() {
        incr = 1
        p2 = countrySeries.getDataItemById($(this).val())
        p2._settings.mapPolygon.set("active",true)
        compareAjax("1", $(this).val(), $("#pays0").val())
    })
})