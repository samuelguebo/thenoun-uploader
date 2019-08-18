/**
 * The current file  handles a set of interactions with the UI
 * and update the UI accordingly
 *
 * @author Samuel Guebo <@samuelguebo>
 * @version 0.1
 * Copyright 2019
 */

'use strict'

// variables
var apiUrl = "http://localhost/php-restapi-vanilla"
var form = document.getElementById( "results-form" )
var table = document.getElementById( "results-table" ).querySelector( "tbody" )

handleCreation();

/**
 * Wrapper function for handling
 * the insertion of new records
 */
function handleCreation() {
  
  // Listen to "Create new item" click
  var btnSearch = document.getElementById( "btn-search" );
  btnSearch.addEventListener( 'click',
  
  function( e ){
    var title = form.querySelector( "input[name=title]" ).value;
    if( title !== "" ){

        /* Make call to API and change table
          if the request is successful */
          fetch(apiUrl + "/product", {
            method: "POST",
            body: { title: username, status: "PENDING" }
          }).then( response => response.json())
          .then((product) => {

            // append product to table
            addRowToTable(product)
            
            // clear input content
            form.querySelector( "input[name=title]" ).value = ""

            // re-attach deletion listener
            handleDeletion();


          }) 
         
    }
    e.preventDefault();
  })

  /**
   * Wrapper function for handling
   * deletions of item list from 
   * the UI
   */
  function handleDeletion() {
    var deletions = document.getElementsByClassName( "action-delete" )
    // Listen to deletion buttion clicks
    for ( var i = 0; i < deletions.length; i++ ) {
      var tableRow = deletions[i];
      tableRow.addEventListener ('click',
      function( e ) {
        console.log( e + " clicked" )
        this.closest( 'tr' ).remove()
      })
    }

  }

  /**
   * Append row to the table
   */
  function addRowToTable(product) {
    // build HTML rows
    var count = table.childNodes.length
    var row = document.createElement( "tr" )
    var rowHTML = "<td>" + ( count++ ) + "</td>"
        rowHTML += "<td>" + product.title + "</td>"
        rowHTML += "<td>" + product.pubDate + "</td>"
        rowHTML += "<td><i class='btn btn-small btn-primary action-delete'>"
        rowHTML += "x</i></td>"
    row.innerHTML = rowHTML
    table.append( row )
  }
}
