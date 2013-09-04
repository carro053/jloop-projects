console.log('JLOOP Leadinator Initialized!');

var environment = 'DEV';
var site_url = 'dev.leads.jloop.com';

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

var appNameContextProperties = {
	"id": "app_name",
	"enabled": false,
	"title": "VIEW AN APP IN THE LEADS SITE BEFORE GATHERING DATA",
	"contexts": ["all"],
	"parentId": parentContextId
};

var emailContextProperties = {
	"id": "save_email",
	"enabled": false,
	"title": "Save email address",
	"onclick": saveEmail,
	"contexts": ["link","selection"],
	"parentId": parentContextId
};

var twitterContextProperties = {
	"id": "save_twitter",
	"enabled": false,
	"title": "Save Twitter link",
	"onclick": saveTwitterUrl,
	"contexts": ["page"],
	"parentId": parentContextId
};

var facebookContextProperties = {
	"id": "save_facebook",
	"enabled": false,
	"title": "Save Facebook link",
	"onclick": saveFacebookUrl,
	"contexts": ["page"],
	"parentId": parentContextId
};

var linkedinContextProperties = {
	"id": "save_linkedin",
	"enabled": false,
	"title": "Save LinkedIn link",
	"onclick": saveLinkedinUrl,
	"contexts": ["page"],
	"parentId": parentContextId
};

var phoneContextProperties = {
	"id": "save_phone",
	"enabled": false,
	"title": "Save phone number",
	"onclick": savePhone,
	"contexts": ["selection"],
	"parentId": parentContextId
};

chrome.contextMenus.create(parentContextProperties, function (){});
chrome.contextMenus.create(scrapeAppContextProperties, function (){});
chrome.contextMenus.create(appNameContextProperties, function (){});
chrome.contextMenus.create(emailContextProperties, function (){});
chrome.contextMenus.create(twitterContextProperties, function (){});
chrome.contextMenus.create(facebookContextProperties, function (){});
chrome.contextMenus.create(linkedinContextProperties, function (){});
chrome.contextMenus.create(phoneContextProperties, function (){});

chrome.runtime.onMessage.addListener(
	function(request, sender, sendResponse) {
		console.log('message received by extension');
		
		if(request.site_url) {
			site_url = request.site_url;
			environment = request.environment;
		} else {
			currentLeadId = request.id;
			
			appNameUpdateProperties = { "title": environment+": Currently gathering data for " + request.name };
			chrome.contextMenus.update("app_name", appNameUpdateProperties, function (){});
			
			emailUpdateProperties = { "enabled": true };
			chrome.contextMenus.update("save_email", emailUpdateProperties, function (){});
			twitterUpdateProperties = { "enabled": true };
			chrome.contextMenus.update("save_twitter", twitterUpdateProperties, function (){});
			fbUpdateProperties = { "enabled": true };
			chrome.contextMenus.update("save_facebook", fbUpdateProperties, function (){});
			phoneUpdateProperties = { "enabled": true };
			linkedinUpdateProperties = { "enabled": true };
			chrome.contextMenus.update("save_linkedin", linkedinUpdateProperties, function (){});
			chrome.contextMenus.update("save_phone", phoneUpdateProperties, function (){});
		}
	});

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
		url: "http://"+site_url+"/Scrapes/create"+"?t="+time,
		data: {"data[Scrape][itunes_link]": scrapeLink, "data[Scrape][is_chrome_extension]": 1},
		success: function(data){
			console.log(data);
			if(data != "1")
				alert('Link couldn\'t be scraped. Are you sure you\'re logged in to the Leads site and clicking an iTunes link?');
		},
		error: function(){
			alert('There was an AJAX error in the extension.');
		}
	});
}

function saveEmail(info, tab) {
	var emailAddress = null;
	if(info.linkUrl) {
		pieces = info.linkUrl.split(':');
		emailAddress = pieces[1];
	} else {
		emailAddress = info.selectionText;
	}
	if(currentLeadId == null) {
		alert('You have to view a lead on the site before you can save its email address!');
		return;
	}

	var time = new Date().getTime();
	$.ajax({
		type: "POST",
		url: "http://"+site_url+"/Leads/update"+"?t="+time,
		data: {"data[Lead][id]": currentLeadId, "data[Lead][email]": emailAddress, "data[Lead][is_chrome_extension]": 1},
		success: function(data){
			console.log(data);
			if(data != "1")
				alert('Something went wrong. We\'re not really sure.');
		},
		error: function(){
			alert('There was an AJAX error in the extension.');
		}
	});
}

function saveTwitterUrl(info, tab) {
	if(currentLeadId == null) {
		alert('You have to view a lead on the site before you can save its Twitter link!');
		return;
	}

	var time = new Date().getTime();
	$.ajax({
		type: "POST",
		url: "http://"+site_url+"/Leads/update"+"?t="+time,
		data: {"data[Lead][id]": currentLeadId, "data[Lead][twitter]": info.pageUrl, "data[Lead][is_chrome_extension]": 1},
		success: function(data){
			console.log(data);
			if(data != "1")
				alert('Something went wrong. We\'re not really sure.');
		},
		error: function(){
			alert('There was an AJAX error in the extension.');
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
		url: "http://"+site_url+"/Leads/update"+"?t="+time,
		data: {"data[Lead][id]": currentLeadId, "data[Lead][facebook]": info.pageUrl, "data[Lead][is_chrome_extension]": 1},
		success: function(data){
			console.log(data);
			if(data != "1")
				alert('Something went wrong. We\'re not really sure.');
		},
		error: function(){
			alert('There was an AJAX error in the extension.');
		}
	});
}

function saveLinkedinUrl(info, tab) {
	if(currentLeadId == null) {
		alert('You have to view a lead on the site before you can save its LinkedIn link!');
		return;
	}

	var time = new Date().getTime();
	$.ajax({
		type: "POST",
		url: "http://"+site_url+"/Leads/update"+"?t="+time,
		data: {"data[Lead][id]": currentLeadId, "data[Lead][linkedin]": info.pageUrl, "data[Lead][is_chrome_extension]": 1},
		success: function(data){
			console.log(data);
			if(data != "1")
				alert('Something went wrong. We\'re not really sure.');
		},
		error: function(){
			alert('There was an AJAX error in the extension.');
		}
	});
}

function savePhone(info, tab) {
	if(currentLeadId == null) {
		alert('You have to view a lead on the site before you can save its Twitter link!');
		return;
	}

	var time = new Date().getTime();
	$.ajax({
		type: "POST",
		url: "http://"+site_url+"/Leads/update"+"?t="+time,
		data: {"data[Lead][id]": currentLeadId, "data[Lead][phone]": info.selectionText, "data[Lead][is_chrome_extension]": 1},
		success: function(data){
			console.log(data);
			if(data != "1")
				alert('Something went wrong. We\'re not really sure.');
		},
		error: function(){
			alert('There was an AJAX error in the extension.');
		}
	});
}

//https://www.google.com/url?sa=t&rct=j&q=&esrc=s&source=web&cd=2&cad=rja&ved=0CDYQFjAB&url=https%3A%2F%2Fitunes.apple.com%2Fus%2Fapp%2Fmatlab-mobile%2Fid370976661%3Fmt%3D8&ei=Wej2UbTCMKPNiwLj1oGoBg&usg=AFQjCNG5LnMMsEyUzSORYuXNQTMSGeuMuQ&sig2=cdHknaEIhDA8TkoDwX6ggg&bvm=bv.49967636,d.cGE