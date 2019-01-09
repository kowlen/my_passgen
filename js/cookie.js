    // функция устанавливающая куки, хранящие состояния checkbox'ов
    function cookieFromCheckbox()
    {
      var ch = [];
      $("#item_opt_4").each(function(){
        var $el = $(this);
        if($el.prop("checked"))
          ch.push($el.attr("id"));
      });
    
      $.cookie("checkboxCookie", ch.join(','));
    }
    
    // функция восстанавливающая состояния checkbox'ов по кукам
    function checkboxFromCookie()
    {
      if($.cookie("checkboxCookie") == null)
        return;
      var chMap = $.cookie("checkboxCookie").split(',');
      for (var i in chMap)
        $('#'+chMap[i]).prop("checked", true);
    }
    
    // функция сбрасывающая куки с значениями checkbox'ов
    function clearCookie()
    {
      $.cookie("checkboxCookie", null);
    } 
    
    // проверим, были ли установлены ранее кукисы с именем checkboxCookie.
    // если нет - установим их.
    var checkboxCookie = $.cookie("checkboxCookie");
    if(checkboxCookie == null)
    {
      cookieFromCheckbox();
      checkboxCookie = $.cookie("checkboxCookie");
    }
    else
      checkboxFromCookie();
    
    $("#item_opt_4").change(function(){
      cookieFromCheckbox();
    });
