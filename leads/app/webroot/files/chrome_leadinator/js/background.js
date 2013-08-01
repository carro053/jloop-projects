console.log('JLOOP Leadinator Initialized!');

var parentContextId = "leadinator_parent";

var currentLeadId = null;

var parentContextProperties = {
	"title": "JLOOP Leadinator",
	"id": parentContextId,
	"contexts": ["all"]
};



var scrapeAppContextProperties = {
	"id": "scrape_app",
	"title": "Scrape this App",
	"onclick": scrapeAppLink,
	"contexts": ["page","link"],
	"parentId": parentContextId
};


var facebookContextProperties = {
	"id": "save_facebook",
	"title": "You must view a lead in your browser before you can save a Facebook link",
	"onclick": saveFacebookUrl,
	"contexts": ["page"],
	"parentId": parentContextId
};

chrome.contextMenus.create(parentContextProperties, function (){});
chrome.contextMenus.create(scrapeAppContextProperties, function (){});
chrome.contextMenus.create(facebookContextProperties, function (){});


chrome.runtime.onMessage.addListener(
	function(request, sender, sendResponse) {
		console.log('message received by extension');
		console.log(request.name);
		
		currentLeadId = request.id;
		fbUpdateProperties = { "title": "Save Facebook link for " + request.name };
		chrome.contextMenus.update("save_facebook", fbUpdateProperties, function (){});
	});




/*
var context_menu_created = false;

chrome.tabs.onUpdated.addListener(function(tabId, changeInfo, tab) {
	toggleContextMenu();
});

chrome.tabs.onActivated.addListener(function(activeInfo) {
	toggleContextMenu();
});

function toggleContextMenu() {
	chrome.tabs.getSelected(null,function(tab) {
		if(tab.url.indexOf('https://www.google.com') !== -1) {
			console.log('attach the context menu');
			if(!context_menu_created) {
				context_menu_created = true;
				var createProperties = {
					"title": "Scrape this App",
					"onclick": scrapeAppLink,
					"contexts": ["link"]
				};
				chrome.contextMenus.create(createProperties, function (){
					
				});
			}
		} else {
			if(context_menu_created) {
				context_menu_created = false;
				chrome.contextMenus.removeAll(function () {
					
				});
			}
		}
	});
}
*/

function scrapeAppLink(info, tab) {
	console.log(info);

	var scrapeLink = null;
	
	if(info.linkUrl)
		scrapeLink = info.pageUrl;
	if(info.linkUrl)
		scrapeLink = info.linkUrl;

	var time = new Date().getTime();
	$.ajax({
		type: "POST",
		url: "http://leads.jloop.com/Scrapes/create"+"?t="+time,
		data: {"data[Scrape][itunes_link]": scrapeLink, "data[Scrape][is_chrome_extension]": 1},
		success: function(data){
			console.log(data);
			if(data != "1")
				alert('Link couldn\'t be scraped. Are you sure you\'re logged in to the Leads site and clicking an iTunes link?');
		},
		error: function(){
			alert('There was a connection error with this scrape attempt.');
		}
	});
}

function saveFacebookUrl(info, tab) {
	if(currentLeadId == null) {
		alert('You have to view a lead on the site before you can save its Facebook link!');
		return;
	}

	var time = new Date().getTime();
	$.ajax({
		type: "POST",
		url: "http://leads.jloop.com/Leads/update"+"?t="+time,
		data: {"data[Lead][id]": currentLeadId, "data[Lead][facebook]": info.pageUrl, "data[Lead][is_chrome_extension]": 1},
		success: function(data){
			console.log(data);
			if(data != "1")
				alert('Something went wrong. We\'re not really sure.');
		},
		error: function(){
			alert('There was a connection error with this scrape attempt.');
		}
	});
}

//https://www.google.com/url?sa=t&rct=j&q=&esrc=s&source=web&cd=2&cad=rja&ved=0CDYQFjAB&url=https%3A%2F%2Fitunes.apple.com%2Fus%2Fapp%2Fmatlab-mobile%2Fid370976661%3Fmt%3D8&ei=Wej2UbTCMKPNiwLj1oGoBg&usg=AFQjCNG5LnMMsEyUzSORYuXNQTMSGeuMuQ&sig2=cdHknaEIhDA8TkoDwX6ggg&bvm=bv.49967636,d.cGE