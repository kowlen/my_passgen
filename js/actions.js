
function updatePasswordAtPage()
{
    $('#password').val(password1);
    $('#btn_refresh').prop('disabled', false);
    $('#btn_refresh_2').prop('disabled', false);
    $('#btn_refresh').attr('style', 'cursor:pointer');
    $('#btn_refresh_2').attr('style', 'cursor:pointer');
    if (!_browser) {
    $('#icon_refresh').attr("class",'fas fa-sync-alt fa-2x');
    $('#icon_refresh_2').attr("class",'fas fa-sync-alt fa-2x');
    }
}

function setWordCombination() {
  let child1Text = $(".w_option_left input:checked + .word_opt");
  let child2Text = $(".w_option_right input:checked + .word_opt");
  $("#word_combination").text($(child1Text).text() + ' + ' + $(child2Text).text());

  let oMidLine = $(".mid_line");
  $(oMidLine).height(Math.abs($(child1Text).position().top - $(child2Text).position().top));
  $(oMidLine).css("top", Math.min($(child1Text).position().top, $(child2Text).position().top) + 13 + "px");
}

/*<--1 чекбокс-->*/
$(".w_option_left input").on("click", function() {
  $('.w_option_left input').not(this).prop('checked', false);
  $(this).prop("checked", true);
  setWordCombination();
});

$(".w_option_right input").on("click", function() {
  $('.w_option_right input').not(this).prop('checked', false);
  $(this).prop("checked", true);
  setWordCombination();
});