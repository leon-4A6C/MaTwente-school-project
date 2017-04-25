function getParams() {
  var params = window.location.search.substring(1);
  if (params == "") {
    return false
  } else {
    var arg = {};
    while (params.indexOf("=") > -1) {
      var value;
      if (params.indexOf("&") > -1) {
        value = params.substring(params.indexOf("=")+1, params.indexOf("&"));
        arg[params.substring(0, params.indexOf("="))] = value.replace(/%20/gi, " ").replace(/%27/gi, "'");
        params = params.substring(params.indexOf("=")+value.length+2, params.length);
      } else {
        arg[params.substring(0, params.indexOf("="))] = params.substring(params.indexOf("=")+1, params.length).replace(/%20/gi, " ").replace(/%27/gi, "'");
        params = params.substring(params.indexOf("=")+1, params.length);
      }
    }
  }
  return arg;
}
var params = getParams();
if (params) {
  console.log(params);
  var inputs = document.getElementsByClassName("inputs");
  document.getElementsByClassName("new-user")[0].innerHTML += "<error>"+params["error"]+"</error>";
  var counter = 0;
  for (var input in params) {
    if (params.hasOwnProperty(input)) {
      if (input != "error" && input != "gender") {
        console.log(input, params[input]);
        inputs[counter].value = params[input];
        counter++;
      } else if (input == "gender") {
        if (params[input] == "m") {
          document.getElementById("male").checked = "true";
        } else {
          document.getElementById("female").checked = "true";
        }
      }
    }
  }
}
