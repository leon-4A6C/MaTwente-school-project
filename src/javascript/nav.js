function updateItems() {
  document.getElementsByTagName("main")[0].style.width = (window.innerWidth - document.getElementsByTagName("nav")[0].clientWidth)+"px";
  document.getElementsByTagName("main")[0].style.minHeight = (window.innerHeight - document.getElementsByClassName("profileBar")[0].clientHeight)+"px";
  document.getElementsByClassName("navArrow")[0].style.left = document.getElementsByTagName("nav")[0].clientWidth+"px";
  requestAnimationFrame(updateItems);
}
updateItems();

// toggles the menu
document.getElementsByClassName("navArrow")[0].addEventListener("click", function(e) {
  if (document.getElementsByClassName("logo")[0].style.display === "none") {
    document.getElementsByClassName("logo")[0].style.display = "initial";
  } else if(document.getElementsByClassName("logo")[0].style.display === "initial") {
    setTimeout(logo, 300);
    function logo() {
      document.getElementsByClassName("logo")[0].style.display = "none";
    }
  }
  e.target.classList.toggle("navArrowOpen");
  document.getElementsByTagName("nav")[0].classList.toggle("navClosed");
  document.getElementsByClassName("logo")[0].classList.toggle("logoGone");
});
