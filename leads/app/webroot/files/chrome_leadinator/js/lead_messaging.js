console.log('hey, i injected, bro');

/*
// The ID of the extension we want to talk to.
var editorExtensionId = "bahjdelimcmkgnembfahpmbbigoecaih";

// Make a simple request:
chrome.runtime.sendMessage(editorExtensionId, {"test" : "test"},
  function(response) {
    
  });
*/

sendLeadInfoToExtension();

function sendLeadInfoToExtension() {
	console.log('sending info');
	chrome.runtime.sendMessage({greeting: "hello"}, function(response) {
	  console.log(response);
	});
}