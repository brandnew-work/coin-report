
/*----------------------------------------------------------
  table-scroll 設定
----------------------------------------------------------*/

window.addEventListener('DOMContentLoaded', () => {
  if (document.querySelector('.table-scroll')) {
    var target = Array.from(document.querySelectorAll('.table-scroll'));
    target.forEach(function (el) {
      el.outerHTML = "<div class='table-scroll__wrap'>" + el.outerHTML + "</div>";
    })
  }

});
