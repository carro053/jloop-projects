//
//  HomeController.m
//  Do We Have Enough
//
//  Created by Jay Dysart on 10/14/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import "HomeController.h"
#import "CreateEventViewController.h"
#import "RootViewController.h"
#import "EventListViewController.h"
#import "EventDetailsViewController.h"
#import "SettingsTracker.h"
#import "ValidateEmailViewController.h"
#import "CheckValidationViewController.h"
#import "Do_We_Have_EnoughAppDelegate.h"
//#import "Beacon.h"; //no longer using



@implementation HomeController
@synthesize createButton, eventsButton, rootController;
@synthesize latestButton;
@synthesize loadingView;
@synthesize latest_event_id;
@synthesize latestHandImage;
@synthesize latestTipImage;
@synthesize latestActivity;
@synthesize settingsButton;

@synthesize webData;
@synthesize xmlParser;

- (void)viewDidLoad {
	NSLog(@"didappear");
	latestButton.hidden = YES;
	latestTipImage.hidden = YES;
	latestHandImage.hidden = YES;
	[self performSelector:@selector(checkValidation) withObject:nil afterDelay:0.1];
	CGRect newSettingsButtonRect = CGRectMake(settingsButton.frame.origin.x-25, 
											  settingsButton.frame.origin.y-25, settingsButton.frame.size.width+50, 
											  settingsButton.frame.size.height+50);
	[settingsButton setFrame:newSettingsButtonRect];
	
	//NSBundle *mainBundle = [NSBundle mainBundle];
	//NSDictionary *infoDict = [mainBundle infoDictionary];
	//NSLog(@"environment: %@", [[[NSBundle mainBundle] infoDictionary] valueForKey:@"WEB_ENVIRONMENT"]);
	
	[super viewDidLoad];
}

- (void)viewDidAppear:(BOOL)animated
{
    NSLog(@"home screen appeared");
    //[self performSelector:@selector(checkValidation) withObject:nil afterDelay:0.1];
}

-(void)swapCheckValidation {
	[self dismissModalViewControllerAnimated:YES];
	[self performSelector:@selector(checkValidation) withObject:nil afterDelay:1];
}

-(void)checkValidation {
	NSLog(@"checkvalidation");
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	if (![settings.emailAddress isEqualToString:@"false"]) {
		NSLog(@"email is set");
		if ([settings.isValidated isEqualToString:@"false"]) {
			NSLog(@"validation is not set");
			CheckValidationViewController *checkValidationController = [[CheckValidationViewController alloc] initWithNibName:@"CheckValidationViewController" bundle:nil];
			[self presentModalViewController:checkValidationController animated:YES];
			[checkValidationController release];

		} else {
			[latestActivity startAnimating];
			NSString * path = [[NSString alloc] initWithFormat:@"http://%@.dowehaveenough.com/devices/get_user.xml", [[[NSBundle mainBundle] infoDictionary] valueForKey:@"WEB_ENVIRONMENT"]];
			[self retrieveXMLFileAtURL:path];
			[path release];
			
		}
	}
	[settings release];
	Do_We_Have_EnoughAppDelegate *appDelegate = (Do_We_Have_EnoughAppDelegate *)[[UIApplication sharedApplication] delegate];
	//NSString *myEventID = [[NSString alloc] initWithFormat:@"%@", appDelegate.launchEventID];
	//NSLog(@"my event id: %@", myEventID);
	if ([appDelegate.launchEventID length] > 1) {
		EventDetailsViewController *myController = [[EventDetailsViewController alloc] initWithStyle:UITableViewStyleGrouped];
		myController.event_id = appDelegate.launchEventID;
		[self.rootController.navigationController pushViewController:myController animated:YES];
		[myController release];
	}
}
- (IBAction)createButtonPressed {
	//[[Beacon shared] startSubBeaconWithName:@"Create Event Started" timeSession:NO];
	CreateEventViewController *createEventController = [[CreateEventViewController alloc] initWithStyle:UITableViewStyleGrouped];
	[self.rootController.navigationController pushViewController:createEventController animated:YES];
	[createEventController release];
}
- (IBAction)latestButtonPressed {
	//[[Beacon shared] startSubBeaconWithName:@"Latest Event" timeSession:NO];
	EventDetailsViewController *myController = [[EventDetailsViewController alloc] initWithStyle:UITableViewStyleGrouped];
	myController.event_id = latest_event_id;
	[self.rootController.navigationController pushViewController:myController animated:YES];
	[myController release];
}
- (IBAction)eventsButtonPressed {
	//[[Beacon shared] startSubBeaconWithName:@"Event List" timeSession:NO];
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	NSString *tempEmail = [[NSString alloc] initWithString:@"false"];
	if ([settings.emailAddress isEqualToString: @"false"]) {
		ValidateEmailViewController *validateController = [[ValidateEmailViewController alloc] initWithNibName:@"ValidateEmailViewController" bundle:nil];
		[self presentModalViewController:validateController animated:YES];
		[validateController release];
	} else {
		EventListViewController *eventListViewController = [[EventListViewController alloc] initWithStyle:UITableViewStyleGrouped];
		[self.rootController.navigationController pushViewController:eventListViewController animated:YES];
		[eventListViewController release];
	}
	[settings release];
	[tempEmail release];
}
-(IBAction)settingsButtonPressed {
	[self.rootController switchViews:self];
}



- (void)didReceiveMemoryWarning {
	// Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
	
	// Release any cached data, images, etc that aren't in use.
}

- (void)viewDidUnload {
	// Release any retained subviews of the main view.
	// e.g. self.myOutlet = nil;
}

#pragma mark POST methods
- (void)retrieveXMLFileAtURL:(NSString *)URL {
	latestButton.titleLabel.text = @"loading . . .";
	UIDevice *device = [UIDevice currentDevice];
	NSString *uniqueIdentifier = [device uniqueIdentifier];
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	NSString *postString = [NSString stringWithFormat:@"email_address=%@&device_id=%@", settings.emailAddress, uniqueIdentifier];
    //NSLog(postString);
    [settings release];
    
    NSURL *url = [NSURL URLWithString:URL];
    NSMutableURLRequest *req = [NSMutableURLRequest requestWithURL:url];
    NSString *msgLength = [NSString stringWithFormat:@"%d", [postString length]];
    
	[req addValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"Content-Type"];
	[req addValue:msgLength forHTTPHeaderField:@"Content-Length"];
    [req setHTTPMethod:@"POST"];
    [req setHTTPBody: [postString dataUsingEncoding:NSUTF8StringEncoding]];
    
    conn = [[NSURLConnection alloc] initWithRequest:req delegate:self];
    if (conn) {
        webData = [[NSMutableData data] retain];
    }
}
-(void) connectionDidFinishLoading:(NSURLConnection *) connection {
    NSLog(@"DONE. Received Bytes: %d", [webData length]);
    NSString *theXML = [[NSString alloc] 
                        initWithBytes: [webData mutableBytes] 
                        length:[webData length] 
                        encoding:NSUTF8StringEncoding];
    //---shows the XML---
    //NSLog(theXML);
	[theXML release];    
    //[activityIndicator stopAnimating];    
    if (xmlParser)
    {
        [xmlParser release];
    }    
    xmlParser = [[NSXMLParser alloc] initWithData: webData];
    [xmlParser setDelegate: self];
    [xmlParser setShouldResolveExternalEntities:YES];
    [xmlParser parse];
    
    [connection release];
	[webData release];
	//[self.loadingView removeView];
}
/*
 - (void)connection:(NSURLConnection *)connection didReceiveResponse:(NSURLResponse *)response {
 //webData = [NSMutableData data];
 }
 */
- (void)connection:(NSURLConnection *)connection didReceiveData:(NSData *)data {
    [webData appendData:data];
}


#pragma mark XML Parsing Delegate Methods
- (void)parserDidStartDocument:(NSXMLParser *)parser {
	NSLog(@"found file and started parsing");
}

- (void)parser:(NSXMLParser *)parser parseErrorOccurred:(NSError *)parseError {
	NSString * errorString = [NSString stringWithFormat:@"Unable to download event feed from web site (Error code %i )", [parseError code]];
	NSLog(@"error parsing XML: %@", errorString);
	
	UIAlertView * errorAlert = [[UIAlertView alloc] initWithTitle:@"Error loading content" message:errorString delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
	[errorAlert show];
	[errorAlert release];
}

- (void)parser:(NSXMLParser *)parser didStartElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName attributes:(NSDictionary *)attributeDict{
	//NSLog(@"found this element: %@", elementName);
	currentElement = [elementName copy];
	
	if ([elementName isEqualToString:@"user_data"]) {
		// clear out our story item caches...
		//item = [[NSMutableDictionary alloc] init];
		currentNotifyIn = [[NSMutableString alloc] init];
		currentNotifyOut = [[NSMutableString alloc] init];
		currentNotifyPush = [[NSMutableString alloc] init];
		currentEventID = [[NSMutableString alloc] init];
		currentEventName = [[NSMutableString alloc] init];
		currentEventWhen = [[NSMutableString alloc] init];
	}
}

- (void)parser:(NSXMLParser *)parser didEndElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName{
	
	//NSLog(@"ended element: %@", elementName);
	//do nothing?
	
}

- (void)parser:(NSXMLParser *)parser foundCharacters:(NSString *)string{
	//NSLog(@"found characters: %@", string);
	// save the characters for the current item...
	if ([currentElement isEqualToString:@"notify_in"]) {
		[currentNotifyIn appendString:string];
	} else if ([currentElement isEqualToString:@"notify_out"]) {
		[currentNotifyOut appendString:string];
	} else if ([currentElement isEqualToString:@"notify_push"]) {
		[currentNotifyPush appendString:string];
	} else if ([currentElement isEqualToString:@"latest_event_id"]) {
		[currentEventID appendString:string];
	} else if ([currentElement isEqualToString:@"latest_event_name"]) {
		[currentEventName appendString:string];
	} else if ([currentElement isEqualToString:@"latest_event_when"]) {
		[currentEventWhen appendString:string];
	}
}

- (void)parserDidEndDocument:(NSXMLParser *)parser {
	
	//[activityIndicator stopAnimating];
	//[activityIndicator removeFromSuperview];
	
	NSLog(@"all done!");
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	[settings saveNotifyIn:currentNotifyIn];
	[settings saveNotifyOut:currentNotifyOut];
	[settings saveNotifyPush:currentNotifyPush];
	NSLog(@"notify in in home view: %d", [settings.notifyIn intValue]);
	[settings release];
	NSLog(@"the event IS: %@", currentEventName);
	NSLog(@"current notify in: %@", currentNotifyIn);
	NSString *btnTitle = [[NSString alloc] initWithFormat:@"%@ - %@", currentEventName, currentEventWhen];
	latestButton.hidden = NO;
	[latestButton setTitle:btnTitle forState:UIControlStateNormal];
	[btnTitle release];
	latestTipImage.hidden = NO;
	latestHandImage.hidden = NO;
	latest_event_id = [currentEventID copy];
	[latestActivity stopAnimating];
}



- (void)dealloc {
    [webData release];
    [xmlParser release];
    
	[createButton release];
	[eventsButton release];
	[rootController release];
	[latestButton release];
	[loadingView release];
	[latest_event_id release];
	[latestHandImage release];
	[latestTipImage release];
	[latestActivity release];
	[settingsButton release];
    [super dealloc];
}


@end
