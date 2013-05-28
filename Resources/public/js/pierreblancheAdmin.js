// Gestion du calendrier

function markAsBooked(date1,date2){
  date1El = jQuery('.dp'+date1+'000');
  date2El = jQuery('.dp'+date2+'000');

  if (date1El.hasClass('bookedlast')) {
    date1El.replaceWith('<span class="dp'+date1+'000 booked">'+date1El.html()+'</span>');
  } else {
    date1El.addClass('bookedfirst');
  }

  if (date2El.hasClass('bookedfirst')) {
    date2El.replaceWith('<span class="dp'+date2+'000 booked">'+date2El.html()+'</span>');
  } else {
    date2El.addClass('bookedlast');
  }
  
  for (idate = date1 + 24*60*60; idate <= date2 - 24*60*60; idate = idate + 24*60*60) {
    // En cas de changement d'heure !
    // Heure d'été à partir du dernier dimanche de mars
    if (!(jQuery('.dp'+idate+'000').length)){
      if (jQuery('.dp'+(idate - 3600)+'000').length){
        idate = idate - 3600;
      }
      if (jQuery('.dp'+(idate + 3600)+'000').length){
        idate = idate + 3600;
      }
    }
    jQuery('.dp'+idate+'000').each(function(index){
      if (!jQuery(this).hasClass('datepick-other-month')) {
        jQuery(this).replaceWith('<span class="'+jQuery(this).attr('class')+' booked">'+jQuery(this).html()+'</span>');
      }
    });
  }
}

jQuery(document).ready(function(){

  jQuery('#calendar').datepick(jQuery.extend({
    rangeSelect: true,
    monthsToShow: [3, 3],
    altField: '#booking_date_from',
    altFormat: 'yyyy-mm-dd',
    firstDay:7,
    todayText:'',
    // onClick: function(date) { 
    // },
    onSelect: function(date) { 
          
      date = jQuery('#booking_date_from').val();
      jQuery('#booking_date_from').val(date.substr(0, 10));
      jQuery('#booking_date_to').val(date.substr(13, 10));

      /*
       *        
       *                        jQuery('#date').addClass('valid');

        //transformer la date en timestamp
        var dateStr = jQuery('#date').val();
        selectedDate = new Date(), 
        selectedDate.setHours(12,0,0);
        selectedDate.setYear(dateStr.substr(0,4));
        selectedDate.setMonth(dateStr.substr(5,2) - 1)
        selectedDate.setDate(dateStr.substr(8,2));
        selectedDate = Math.floor(selectedDate.getTime()/1000);
//        8/03 :: 1299582000
//        26/03 :: 1301137200
        lastSeenDate = jQuery(".datepick-month.last table a:last").attr('class').substr(2,10);

        // Si on sélectionne la première seulement
        if (1) {
              //boucle sur les resevartion
              var desactivateFromDate = 9999999999999;
              for (var idx = 0; idx < alldata.resas.length; idx++) {
                //Si la reservation est la première après la date choisie,
//                alert (alldata.resas[idx].date1 ) 
                if (alldata.resas[idx].date2 < desactivateFromDate && alldata.resas[idx].date2 > selectedDate && alldata.resas[idx].date2 <= lastSeenDate) {
                  // On recupère la plus proche suivante
                  var desactivateFromDate = alldata.resas[idx].date2
                }
              
              }
//              alert(lastSeenDate +' - '+ desactivateFromDate +' = '+(lastSeenDate - desactivateFromDate));
              // On désactive les dates qui suivent
              for (idate = desactivateFromDate; idate <= lastSeenDate; idate = idate + 24*60*60) {
                if (idate == desactivateFromDate ) {
                  alert("jQuery('.dp"+idate+"000').replaceWith('<span ></span>');");
                }
                jQuery('.dp1301137200000').addClass('noClick');
                jQuery('.dp1301137200000').replaceWith('<span class="'+jQuery('.dp1301137200000').attr('class')+'"></span>');
                jQuery('.dp'+idate+'000').replaceWith('<span></span>');
//                jQuery('.dp'+idate+'000').replaceWith('<span >'+jQuery('.dp'+idate+'000').html()+'</span>');
              }
        }
       */
    },
    onChangeMonthYear: function(year, month) { 

      // Afficher les dates reservés qui sont déjà connues
      for (var idx = 0; idx < alldata.resas.length; idx++) {
        markAsBooked(alldata.resas[idx][0],alldata.resas[idx][1]);
      }
          
      if (jQuery.inArray(year*100+month,alldata.ids) == -1) {
        // call to json data
        var src = Routing.generate('limitedList', {dateFrom: year+"-"+month, period: 9});
        jQuery.ajax({
          url: src,
          success: function(data) {
            data = eval(data);                
            alldata.ids[alldata.ids.length] = year*100+month;
            for (var idx = 0; idx < data.length; idx++) {
              markAsBooked(data[idx][0],data[idx][1]);

              alldata.resas[alldata.resas.length] = data[idx];
            }
          }
        });
      }

    }
  },
  jQuery.datepick.regional['fr']
));

});
 

 
 


// Gestion des formulaires 
jQuery(document).ready(function(){

  jQuery("input.textTitle").each(function(){
    if (jQuery(this).attr('title')) {
      jQuery(this).attr('value',jQuery(this).attr('title'));
    }
  })

  jQuery(".pregValidate").focusin(function(){
    jQuery(this).removeClass('valid');
    jQuery(this).removeClass('error');
    jQuery(this).addClass('editing');
    if (jQuery(this).attr('value')==jQuery(this).attr('title')) {
      jQuery(this).attr('value','');
      //      jQuery('#booking .submit').removeClass('clickable');
    } else {
    }
  }).focusout(function(){
    jQuery(this).removeClass('editing');
    var val = jQuery(this).attr('value');
    val = jQuery.trim(val);
    jQuery(this).attr('value',val);
    if (val==jQuery(this).attr('title').substr(0,val.length) || val=="") {
      jQuery(this).attr('value',jQuery(this).attr('title'));
      jQuery(this).removeClass('valid');
    } else {
      var reg = eval(jQuery(this).attr('data'));
      if (jQuery(this).attr('value').match(reg)) {
        jQuery(this).addClass('valid');
        //        jQuery('#booking .submit').removeClass('clickable');
      } else  {
        jQuery(this).addClass('error');
        //        jQuery('#booking .submit').addClass('clickable');
      }
    }
  });

  jQuery('form').submit(function(e){
    var error = false;
    e.preventDefault();
    jQuery(this).children('.pregValidate').each(function(input){
      var inputE = jQuery(input);
      inputE.trigger('focusout');
      if (inputE.attr("type") != 'submit') {
        if (inputE.hasClass('error')) { error = true;}
      }
    });
    if (error) {
      alert (this.get('data'));
    } else { 
      el = jQuery(this);
      jQuery.post(el.attr('action'), el.serialize(), function(data,result,httprequest){
        el.after("<div id='adata'>"+data+"</div>"); 
        el.css("display","none");
      });
    }
  });

});
  
