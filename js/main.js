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
var btnSearch = document.getElementById( "btn-search" );
var btnSearch = document.getElementById( "btn-search" );
var form = document.getElementById( "results-form" )
var table = document.getElementById( "results-table" ).querySelector( "tbody" )
var deletions = document.getElementsByClassName( "action-delete" )

// Listen to "Create new item" click
btnSearch.addEventListener( 'click',
function( e ){
  var username = form.querySelector( "input[name=username]" ).value
  form.querySelector( "input[name=username]" ).value = ""

  // build HTML rows
  var count = table.childNodes.length
  var row = document.createElement( "tr" )
  var today = new Date()
  var rowHTML = "<td>" + ( count++ ) + "</td>"
      rowHTML += "<td>" + username + "</td>"
      rowHTML += "<td>" + today.getDate() + "-" + ( today.getMonth() + 1 )
      rowHTML += "-" + today.getFullYear() + "</td>"
      rowHTML += "<td><i class='btn btn-small btn-primary action-delete'>"
      rowHTML += "x</i></td>"
  row.innerHTML = rowHTML
  table.append( row )

  // Listen to deletion buttion clicks
  for ( var i = 0; i < deletions.length; i++ ) {
    var tableRow = deletions[i];
    tableRow.addEventListener ('click',
    function( e ) {
      console.log( e + " clicked" )
      this.closest( 'tr' ).remove()
    })
  }
  e.preventDefault();
})
