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
#import "TestFlight.h"
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
@synthesize bgImage;

@synthesize viewAppeared;

- (void)viewDidLoad {
    Do_We_Have_EnoughAppDelegate *appDelegate = (Do_We_Have_EnoughAppDelegate *)[[UIApplication sharedApplication] delegate];
    appDelegate.homeController = self;
    CGRect screenBound = [[UIScreen mainScreen] bounds];
    CGSize screenSize = screenBound.size;
    CGFloat screenHeight = screenSize.height;
    if(screenHeight == 568.0)
    {
        self.view.frame = CGRectMake(self.view.frame.origin.x,self.view.frame.origin.y, 320.0, 548.0);
        bgImage.frame = CGRectMake(bgImage.frame.origin.x,bgImage.frame.origin.y, 320.0, 548.0);
        bgImage.image = [UIImage imageNamed: @"home_bg-568h.png"];
        latestTipImage.frame = CGRectMake(latestTipImage.frame.origin.x,latestTipImage.frame.origin.y + 88, latestTipImage.frame.size.width, latestTipImage.frame.size.height);
        latestHandImage.frame = CGRectMake(latestHandImage.frame.origin.x,latestHandImage.frame.origin.y + 88, latestHandImage.frame.size.width, latestHandImage.frame.size.height);
        latestActivity.frame = CGRectMake(latestActivity.frame.origin.x,latestActivity.frame.origin.y + 88, latestActivity.frame.size.width, latestActivity.frame.size.height);
        latestButton.frame = CGRectMake(latestButton.frame.origin.x,latestButton.frame.origin.y + 88, latestButton.frame.size.width, latestButton.frame.size.height);
    }
    viewAppeared = NO;
    
    
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
	
    [TestFlight passCheckpoint:@"HOME VIEW"];
    
	[super viewDidLoad];
}

- (void)viewDidAppear:(BOOL)animated
{
    if(!viewAppeared)
    {
        latestButton.hidden = YES;
        latestTipImage.hidden = YES;
        [self performSelector:@selector(checkValidation) withObject:nil afterDelay:0.2];
        viewAppeared = YES;
    }
}
- (void)viewWillAppear:(BOOL)animated
{
    //latestButton.hidden = YES;
    //latestTipImage.hidden = YES;
    //[self performSelector:@selector(checkValidation) withObject:nil afterDelay:0.2];
}

-(void)viewWillDisappear:(BOOL)animated
{
    viewAppeared = NO;
}

-(void)swapCheckValidation {
	[self dismissViewControllerAnimated:YES completion:nil];
	[self performSelector:@selector(checkValidation) withObject:nil afterDelay:1];
}

-(void)checkValidation {
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	if (![settings.emailAddress isEqualToString:@"false"]) {
        [TestFlight passCheckpoint:@"HOME VIEW EMAIL IS SET"];
		NSLog(@"email is set");
		if ([settings.isValidated isEqualToString:@"false"]) {
            [TestFlight passCheckpoint:@"HOME VIEW VALIDATION NOT SET"];
			NSLog(@"validation is not set");
			CheckValidationViewController *checkValidationController = [[CheckValidationViewController alloc] initWithNibName:@"CheckValidationViewController" bundle:nil];
            [self presentViewController:checkValidationController animated:YES completion:nil];
			[checkValidationController release];

		} else {
            [TestFlight passCheckpoint:@"HOME VIEW VALIDATION IS SET"];
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
	if ([appDelegate.launchEventID  intValue] > 0) {
        bool goTo = true;
        EventDetailsViewController *myController = [[EventDetailsViewController alloc] initWithStyle:UITableViewStyleGrouped];
        [TestFlight passCheckpoint:@"HOME VIEW LAUNCHED WITH EVENT ID"];
        NSArray *controllers = self.rootController.navigationController.viewControllers;
        if([controllers count] > 1)
        {
            if([[controllers objectAtIndex:1] isKindOfClass:[myController class]])
            {
                EventDetailsViewController *firstController = (EventDetailsViewController*)[controllers objectAtIndex:1];
                if([appDelegate.launchEventID intValue] == [firstController.event_id intValue])
                    goTo = false;
            }else if([[controllers objectAtIndex:2] isKindOfClass:[myController class]])
            {
                EventDetailsViewController *secondController = (EventDetailsViewController*)[controllers objectAtIndex:2];
                if([appDelegate.launchEventID intValue] == [secondController.event_id intValue])
                    goTo = false;
            }
        }
        if(goTo)
        {
            myController.event_id = appDelegate.launchEventID;
            [self.rootController.navigationController pushViewController:myController animated:YES];
        }
        [myController release];
	}
    appDelegate.launchEventID = @"";
}
- (IBAction)createButtonPressed {
	//[[Beacon shared] startSubBeaconWithName:@"Create Event Started" timeSession:NO];
    [TestFlight passCheckpoint:@"HOME VIEW CREATE BUTTON PRESSED"];
	CreateEventViewController *createEventController = [[CreateEventViewController alloc] initWithStyle:UITableViewStyleGrouped];
	[self.rootController.navigationController pushViewController:createEventController animated:YES];
	[createEventController release];
}
- (IBAction)latestButtonPressed {
	//[[Beacon shared] startSubBeaconWithName:@"Latest Event" timeSession:NO];
    [TestFlight passCheckpoint:@"HOME VIEW LATEST BUTTON PRESSED"];
	EventDetailsViewController *myController = [[EventDetailsViewController alloc] initWithStyle:UITableViewStyleGrouped];
	myController.event_id = latest_event_id;
	[self.rootController.navigationController pushViewController:myController animated:YES];
	[myController release];
}
- (IBAction)eventsButtonPressed {
	//[[Beacon shared] startSubBeaconWithName:@"Event List" timeSession:NO];
    [TestFlight passCheckpoint:@"HOME VIEW EVENTS BUTTON PRESSED"];
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	if ([settings.emailAddress isEqualToString: @"false"]) {
		ValidateEmailViewController *validateController = [[ValidateEmailViewController alloc] initWithNibName:@"ValidateEmailViewController" bundle:nil];
        [self presentViewController:validateController animated:YES completion:nil];
		[validateController release];
	} else {
        if ([settings.isValidated isEqualToString:@"false"]) {
			CheckValidationViewController *checkValidationController = [[CheckValidationViewController alloc] initWithNibName:@"CheckValidationViewController" bundle:nil];
            [self presentViewController:checkValidationController animated:YES completion:nil];
			[checkValidationController release];
		} else {
            EventListViewController *eventListViewController = [[EventListViewController alloc] initWithStyle:UITableViewStyleGrouped];
            [self.rootController.navigationController pushViewController:eventListViewController animated:YES];
            [eventListViewController release];
        }
	}
	[settings release];
}
-(IBAction)settingsButtonPressed {
    [TestFlight passCheckpoint:@"HOME VIEW SETTINGS BUTTON PRESSED"];
    SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
    if (![settings.emailAddress isEqualToString:@"false"]) {
		if ([settings.isValidated isEqualToString:@"false"]) {
			CheckValidationViewController *checkValidationController = [[CheckValidationViewController alloc] initWithNibName:@"CheckValidationViewController" bundle:nil];
			//[self presentModalViewController:checkValidationController animated:YES];
            [self presentViewController:checkValidationController animated:YES completion:nil];
			[checkValidationController release];
		} else {
            [self.rootController switchViews:self];
		}
	}else{
		ValidateEmailViewController *validateController = [[ValidateEmailViewController alloc] initWithNibName:@"ValidateEmailViewController" bundle:nil];
		//[self presentModalViewController:validateController animated:YES];
        [self presentViewController:validateController animated:YES completion:nil];
		[validateController release];
    }
    [settings release];
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
    [latestButton setTitle:@"       loading..." forState:UIControlStateNormal];
    
	//UIDevice *device = [UIDevice currentDevice];
	//NSString *uniqueIdentifier = [device uniqueIdentifier];
    
    NSString *uniqueIdentifier = [UIDevice currentDevice].identifierForVendor.UUIDString;
    
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	NSString *postString = [NSString stringWithFormat:@"email_address=%@&device_id=%@", settings.emailAddress, uniqueIdentifier];
    //NSLog(@"%@ %@",URL,postString);
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
    //NSLog(@"get file");
}
-(void) connectionDidFinishLoading:(NSURLConnection *) connection {
    NSLog(@"DONE. Received Bytes: %d", [webData length]);
    NSString *theXML = [[NSString alloc] 
                        initWithBytes: [webData mutableBytes] 
                        length:[webData length] 
                        encoding:NSUTF8StringEncoding];
    //---shows the XML---
    
    if([theXML isEqualToString:@"<?xml version=\"1.0\" encoding=\"UTF-8\"?><result>false</result>"])
    {
        SettingsTracker *settings = [[SettingsTracker alloc] init];
        [settings initData];
        [settings saveValidation:@"false"];
        [settings saveEmail:@"false"];
        [settings release];
        [self settingsButtonPressed];
    }else{
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
    }
    
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
	NSLog(@"DID START");
	if ([elementName isEqualToString:@"user_data"]) {
		// clear out our story item caches...
		//item = [[NSMutableDictionary alloc] init];
		currentNotifyIn = [[NSMutableString alloc] init];
		currentNotifyOut = [[NSMutableString alloc] init];
		currentNotifyEventChange = [[NSMutableString alloc] init];
		currentAppNotifyIn = [[NSMutableString alloc] init];
		currentAppNotifyOut = [[NSMutableString alloc] init];
		currentAppNotifyEventChange = [[NSMutableString alloc] init];
		currentUserName = [[NSMutableString alloc] init];
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
    string = [string stringByTrimmingCharactersInSet:[NSCharacterSet newlineCharacterSet]];
    
	if ([currentElement isEqualToString:@"notify_in"]) {
		[currentNotifyIn appendString:string];
	} else if ([currentElement isEqualToString:@"notify_out"]) {
		[currentNotifyOut appendString:string];
	} else if ([currentElement isEqualToString:@"notify_event_change"]) {
		[currentNotifyEventChange appendString:string];
    } else if ([currentElement isEqualToString:@"app_notify_in"]) {
        [currentAppNotifyIn appendString:string];
    } else if ([currentElement isEqualToString:@"app_notify_out"]) {
        [currentAppNotifyOut appendString:string];
    } else if ([currentElement isEqualToString:@"app_notify_event_change"]) {
        [currentAppNotifyEventChange appendString:string];
    } else if ([currentElement isEqualToString:@"name"]) {
        [currentUserName appendString:string];
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
	[settings saveNotifyEventChange:currentNotifyEventChange];
	[settings saveAppNotifyIn:currentAppNotifyIn];
	[settings saveAppNotifyOut:currentAppNotifyOut];
	[settings saveAppNotifyEventChange:currentAppNotifyEventChange];
	[settings saveUserName:currentUserName];
    //NSLog(@"%@ %@ %@ %@ %@ %@ %@",currentNotifyIn,currentNotifyOut,currentNotifyEventChange,currentAppNotifyIn,currentAppNotifyOut,currentAppNotifyEventChange,currentUserName);
	[settings release];
	NSLog(@"the event IS: %@", currentEventName);
	NSLog(@"current notify in: %@", currentNotifyIn);
    
    //remove newline characters from currentEventName
    NSString *btnTitle = [[NSString alloc] initWithFormat:@"%@ - %@", currentEventName, currentEventWhen];
    btnTitle = [btnTitle stringByReplacingOccurrencesOfString:@"\n" withString:@""];
    
	latestButton.hidden = NO;
	[latestButton setTitle:btnTitle forState:UIControlStateNormal];
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
    [bgImage release];
    [super dealloc];
}


@end
