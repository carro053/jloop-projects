console.log('hey, i injected, bro');

/*
// The ID of the extension we want to talk to.
var editorExtensionId = "bahjdelimcmkgnembfahpmbbigoecaih";

// Make a simple request:
chrome.runtime.sendMessage(editorExtensionId, {"test" : "test"},
  function(response) {
    
  });
*/

/*
console.log('current val: '+$('#chrome-extension-info').val());

$('#chrome-extension-info').change( function() {
	console.log('sending info');
	console.log($(this).val());
	chrome.runtime.sendMessage({greeting: "hello"}, function(response) {
		console.log(response);
	});
});
*/

/*
$('body').bind('DOMSubtreeModified',testHandler);

function testHandler() {
	console.log('BODY MODIFIED');
	console.log('current val: '+$('#chrome-extension-info').val());
}
*/

//var port = chrome.runtime.connect();


//pass data along to the extension
window.addEventListener("message", function(event) {
	console.log('message received');
	console.log(event);
	
	chrome.runtime.sendMessage(event.data, function(response) {
		console.log('message send to extension');
		console.log(response);
	});
	
}, false);