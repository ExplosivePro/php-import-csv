var endless = {
  // (A) PROPERTIES
  url : "infinite.php", // CHANGE THIS TO YOUR OWN!
  first : true, // LOADING FIRST PAGE?
  proceed : true, // OK TO LOAD MORE PAGES? "LOCK" TO PREVENT LOADING MULTIPLE PAGES
  page : 0, // CURRENT PAGE
  hasMore : true, // HAS MORE CONTENT TO LOAD?
  summary: {},
  init : function () {
      for (var i = 0; i < columns.length; i++) {
        endless.summary[columns[i]] = '';
      }

      $( ".tbl-content" ).scroll(function() {
          var tableHeight = $( ".tbl-content thead").height() + $( ".tbl-content tbody").height();
          var wrapperHeight = $( ".tbl-content").height();
          
          if (tableHeight < wrapperHeight +  $( ".tbl-content" ).scrollTop() + 100) {
              endless.load();
          }
        })
      endless.load();
  },

  // (C) AJAX LOAD CONTENT
  load : function () { if (endless.proceed && endless.hasMore) {
    endless.proceed = false;

    for (var i = 0; i < numerics.length; i++) {
      endless.summary[numerics[i]] = endless.summary[numerics[i]] === '' ? 0 : endless.summary[numerics[i]];
    }
    
    var data = new FormData();
    
    data.append("offset", endless.page);
    data.append("search", search);
    endless.page++;

    for (var i = 0; i < numerics.length; i++) {
      data.append("numerics[]", numerics[i]);
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", endless.url);
    
    xhr.onload = function () {
      let data = JSON.parse(this.response);      
      let limit = data.length;

      console.log(endless.summary);

      for (var i = 0; i < limit; i++) {
        var tr = "";
        for (var j = 0; j < columns.length; j++) {
          
          if (endless.summary[columns[j]] !== '') {
            endless.summary[columns[j]] += Number(data[i][columns[j]]);
          }

          tr += `<td>${data[i][columns[j]]}</td>`;
        }
        $('.tbl-content tbody').append(
          `<tr>${tr}</tr>`
        );
      }

      if (data.length < 30) {
        endless.hasMore = false;

        var tr = "";

        for (var i = 0; i < columns.length; i++) {
          tr += `<td>${endless.summary[columns[i]]}</td>`;
        }  

        $('.tbl-content tbody').append(
          `<tr>${tr}</tr>`
        );


      } else {
        endless.proceed = true; // UNLOCK
        endless.page = nextPg; // UPDATE CURRENT PAGE
      }
    };
    xhr.send(data);
  }}
};
window.addEventListener("DOMContentLoaded", endless.init);
