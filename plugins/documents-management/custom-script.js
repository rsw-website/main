function showConfirmBox(){
  debugger;
  var choice = confirm("You are about to permanently delete these items from your site.\n This action cannot be undone.\n 'Cancel' to stop, 'OK' to delete.");
    if(choice === true) {
        return true;
    }
    return false;
}

// function bulkAction(){
//   debugger;
// }



jQuery(document).ready(function(){
  jQuery('#document-filter').on('submit', function(event){
    debugger;
    var topSelect = jQuery(this).find('select#bulk-action-selector-top')
    .find('option:selected').val();
    var bottomSelect = jQuery(this).find('select#bulk-action-selector-bottom')
    .find('option:selected').val();
    if(topSelect === 'bulk-delete' || bottomSelect === 'bulk-delete'){
      showConfirmBox();
    } else{
      event.preventDefault();
    }
  });
});