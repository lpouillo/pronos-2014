function parseRSS(url, container) {
  $.ajax({
    url: document.location.protocol + '//ajax.googleapis.com/ajax/services/feed/load?v=2.0&num=5&callback=?&q=' + encodeURIComponent(url),
    dataType: 'json',
    success: function(data) {
      //console.log(data.responseData.feed);
      $(container).html('<h4>'+capitaliseFirstLetter(data.responseData.feed.title)+'</h4>');
 
      $.each(data.responseData.feed.entries, function(key, value){
        var thehtml = '<li><a style="text-decoration:none;" href="'+value.link+'" target="_blank">'+value.title+'</a></li>';
        $(container).append(thehtml);
      });

    }
  });
}


/**
 * Capitalizes the first letter of any string variable
 * source: http://stackoverflow.com/a/1026087/477958
 */
function capitaliseFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}
