console.log('hey, i injected, bro');

/*
// The ID of the extension we want to talk to.
var editorExtensionId = "bahjdelimcmkgnembfahpmbbigoecaih";

// Make a simple request:
chrome.runtime.sendMessage(editorExtensionId, {"test" : "test"},
  function(response) {
    
  });
*/

var chris_face = 100;

//EXTENSION.version = 2;
console.log(this);


sendLeadInfoToExtension();

function sendLeadInfoToExtension() {
	console.log('sending info');
	chrome.runtime.sendMessage({greeting: "hello"}, function(response) {
	  console.log(response);
	});
}