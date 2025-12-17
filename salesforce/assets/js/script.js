
$(document).ready(function()
{
	
	var currentItem = 1;
	var obj='#itemCode'+currentItem;
	function callAutocomplete(obj)
	{
$('#itemCode'+currentItem+'').autocomplete({
source: '../assets/data/item-data.php',
minLength: 1,
select: function(event, ui) {
    var $itemrow = $(this).closest('tr');

            		$itemrow.find('#itemCode'+currentItem+'').val(ui.item.itemCode);
                    //$itemrow.find('#itemAvailable'+currentItem+'').val(ui.item.itemAvailable);
                    $itemrow.find('#itemPrice'+currentItem+'').val(ui.item.itemPrice);
					 $itemrow.find('#itemSku'+currentItem+'').val(ui.item.itemSku);
					  $itemrow.find('#itemId'+currentItem+'').val(ui.item.itemId);
					 //$itemrow.find('#itemTax'+currentItem+'').val(ui.item.itemTax);


            $('#itemQty').focus();

    return false;
}

}).data( "autocomplete" )._renderItem = function( ul, item ) {
return $( "<li></li>" )
    .data( "item.autocomplete", item )
    .append( "<a>" + item.itemCode + " - " + item.itemPrice + "</a>" )
    .appendTo( ul );
};

	}

$('#addRow').click(function(){
    currentItem++;
   var strToAdd = '<tr class="item-row" id='+ currentItem +'><td><a id="deleteRow"><img src="../img/icon-minus.png" alt="Remove Item" title="Remove Item"></a></td><td><input name="itemCode[]" value="" class="tInput itemCode" id="itemCode'+currentItem+'" onblur="sendstore('+currentItem+'); sendtax('+currentItem+')" /><input type="hidden" name="itemId[]" value="" class="tInput itemId" id="itemId'+currentItem+'" /> </td><td><input name="itemAvailable[]" value="" class="tInput itemAvailable" id="itemAvailable'+currentItem+'"  readonly="readonly"  /></td><td><input name="itemQty[]" value="" class="tInput itemQty" id="itemQty'+currentItem+'" onkeyup="calculate('+currentItem+'); caltotal()"  /></td><td><input name="itemPrice[]" value="" class="tInput itemPrice" id="itemPrice'+currentItem+'" readonly="readonly"/><input name="itemSku[]" type="hidden" value="" class="tInput" id="itemSku'+currentItem+'"/> </td><td><input name="itemSubtotal[]" value="" class="tInput itemSubtotal" id="itemSubtotal'+currentItem+'" readonly="readonly" onchange="funcaltotal(this.value)"/></td><td><input name="itemDiscount[]" value="" class="tInput itemDiscount" id="itemDiscount'+currentItem+'"  onkeyup="calculate('+currentItem+'); caltotal()" /></td><td><select name="itemTax[]" value=""  id="itemTax'+currentItem+'" readonly="readonly" onChange="calculate('+currentItem+'); caltotal()"></select></td><td> <input type="text" name="itemTaxAmt[]" id="itemTaxAmt'+currentItem+'" readonly="readonly"></td><td> <input type="text" name="itemTotal[]" id="itemTotal'+currentItem+'" readonly="readonly"></td></tr>';

    $('#itemsTable').append(strToAdd);
	document.getElementById('num').value=currentItem;
callAutocomplete("#itemCode" +  currentItem);


});
	var bindfield=$('#itemCode'+currentItem+'');
    var $itemAvailable 	     = $('#itemAvailable'+currentItem+'');
	var $itemDiscount 	     = $('#itemDiscount'+currentItem+'');
	 var $itemTax 	        = $('#itemTax'+currentItem+'');
	 var $itemQty	        = $('#itemQty'+currentItem+'');
      var $itemPrice	        = $('#itemPrice'+currentItem+'');
	  var $itemId	        = $('#itemId'+currentItem+'');
	  var $itemTotal 	        = $('#itemTotal'+currentItem+'');
	bindfield.each(function(){
    $('#itemCode'+currentItem+'').autocomplete({
							
    source: '../assets/data/item-data.php',
    minLength: 1,
    select: function( event, ui ) {             
         var $itemrow = $(this).closest('tr');
            $itemrow.find(bindfield).val(ui.item.itemCode);
			//$itemrow.find(itemAvailable).val(ui.item.itemAvailable);
			 $itemrow.find(itemPrice).val(ui.item.itemPrice);
			  $itemrow.find(itemId).val(ui.item.itemId);
            	$itemrow.find(itemTax).val(ui.item.itemTax);
           
		

            $(itemQty).focus();

    return false;
    }
});
    })
$('.itemCode').focus(function(){
window.onbeforeunload = function(){ return "You haven't saved your data.  Are you sure you want to leave this page without saving first?"; };
});

$("#deleteRow").live('click',function(){
		$(this).parents('.item-row').remove();
		currentItem--;
        // Hide delete Icon if we only have one row in the list.
        if ($(".item-row").length < 2) $("#deleteRow").hide();
	});

});