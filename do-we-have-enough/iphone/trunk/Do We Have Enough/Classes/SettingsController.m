//
//  SettingsController.m
//  Do We Have Enough
//
//  Created by Jay Dysart on 10/14/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import "SettingsController.h"
#import "RootViewController.h"
#import "SettingsTracker.h"
#import "LoadingView.h"
#import "SettingsTracker.h"


@implementation SettingsController
@synthesize resetButton;
@synthesize closeButton;
@synthesize rootController;
@synthesize emailAddressLabel;
@synthesize pushSwitch;
@synthesize notifyInSwitch;
@synthesize notifyOutSwitch;
@synthesize loadingView;

-(IBAction)resetButtonPressed:(id)sender
{
	UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Remove Email?" 
				message:@"Click 'OK' if you wish to remove the association with this email address." 
				delegate:self 
				cancelButtonTitle:@"Cancel" 
				otherButtonTitles:@"OK", nil];
	[alert show];
	[alert release];
}
-(IBAction)closeButtonPressed {
	[self.rootController switchViews:self];
}
-(IBAction)togglePushSwitch:(id)sender
{
	LoadingView *myLoadingView = [LoadingView loadingViewInView:[self.view.window.subviews objectAtIndex:0]];
	loadingView = myLoadingView;
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	NSString *myNotify = [[NSString alloc] initWithFormat:@"%d", [sender isOn]];
	[settings saveNotifyPush:myNotify];
	[myNotify release];
	[settings release];
	[self saveNotifications];
}
-(IBAction)toggleInSwitch:(id)sender
{
	LoadingView *myLoadingView = [LoadingView loadingViewInView:[self.view.window.subviews objectAtIndex:0]];
	loadingView = myLoadingView;
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	NSString *myNotify = [[NSString alloc] initWithFormat:@"%d", [sender isOn]];
	[settings saveNotifyIn:myNotify];
	[myNotify release];
	[settings release];
	[self saveNotifications];
}
-(IBAction)toggleOutSwitch:(id)sender
{
	LoadingView *myLoadingView = [LoadingView loadingViewInView:[self.view.window.subviews objectAtIndex:0]];
	loadingView = myLoadingView;
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	NSString *myNotify = [[NSString alloc] initWithFormat:@"%d", [sender isOn]];
	[settings saveNotifyOut:myNotify];
	[myNotify release];
	[settings release];
	[self saveNotifications];
}

-(void)resetEmailSettings
{
	LoadingView *myLoadingView = [LoadingView loadingViewInView:[self.view.window.subviews objectAtIndex:0]];
	loadingView = myLoadingView;
	[self saveResetEmailSettings];
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings resetData];
	[settings release];
	[self setStage];
	
}
-(void)setStage
{
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	if ([settings.emailAddress isEqualToString:@"false"]) {
		emailAddressLabel.text = @"not set";
		pushSwitch.enabled = NO;
		resetButton.enabled = NO;
		notifyInSwitch.enabled = NO;
		notifyOutSwitch.enabled = NO;
	} else {
		emailAddressLabel.text = settings.emailAddress;
		pushSwitch.enabled = YES;
		resetButton.enabled = YES;
		notifyInSwitch.enabled = YES;
		notifyOutSwitch.enabled = YES;
		[notifyInSwitch setOn:[settings.notifyIn intValue]];
		[notifyOutSwitch setOn:[settings.notifyOut intValue]];
		[pushSwitch setOn:[settings.notifyPush intValue]];
		NSLog(@"notify in in settings view: %@", settings.notifyIn);
	}
	//emailAddressLabel.text = settings.emailAddress;
	[settings release];
	
}



// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad {
	//[self setStage];
    [super viewDidLoad];
}
-(void)viewDidAppear:(BOOL)animated {
	[self setStage];
	[super viewDidAppear:animated];
}
-(void)viewWillAppear:(BOOL)animated {
	//[self setStage];
	[super viewWillAppear:animated];
}


/*
// Override to allow orientations other than the default portrait orientation.
- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation {
    // Return YES for supported orientations
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}
*/

- (void)didReceiveMemoryWarning {
	// Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
	
	// Release any cached data, images, etc that aren't in use.
}
#pragma mark Alert View Delegate
- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex {
	if (buttonIndex == 1) [self resetEmailSettings];
}


- (void)viewDidUnload {
	// Release any retained subviews of the main view.
	// e.g. self.myOutlet = nil;
}
#pragma mark POST methods
- (void)saveNotifications {
	NSString *URL = [[NSString alloc] initWithFormat:@"http://%@.dowehaveenough.com/devices/save_user.xml", [[[NSBundle mainBundle] infoDictionary] valueForKey:@"WEB_ENVIRONMENT"]];
	UIDevice *device = [UIDevice currentDevice];
	NSString *uniqueIdentifier = [device uniqueIdentifier];
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	NSString *postString = [NSString stringWithFormat:@"email_address=%@&device_id=%@&notify_in=%@&notify_out=%@&notify_push=%@", settings.emailAddress, uniqueIdentifier, settings.notifyIn, settings.notifyOut, settings.notifyPush];
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
	[URL release];
}
- (void)saveResetEmailSettings {
	NSString *URL = [[NSString alloc] initWithFormat:@"http://%@.dowehaveenough.com/devices/invalidate_device.xml", [[[NSBundle mainBundle] infoDictionary] valueForKey:@"WEB_ENVIRONMENT"]];
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
	[URL release];
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
	[self.loadingView removeView];
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
	NSString * errorString = [NSString stringWithFormat:@"Unable to set status (Error code %i )", [parseError code]];
	NSLog(@"error parsing XML: %@", errorString);
	
	UIAlertView * errorAlert = [[UIAlertView alloc] initWithTitle:@"Error loading content" message:errorString delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
	[errorAlert show];
	[errorAlert release];
}

- (void)parser:(NSXMLParser *)parser didStartElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName attributes:(NSDictionary *)attributeDict{
	//NSLog(@"found this element: %@", elementName);
	currentElement = [elementName copy];
	
	if ([elementName isEqualToString:@"result"]) {
		// clear out our story item caches...
		//item = [[NSMutableDictionary alloc] init];
		currentResult = [[NSMutableString alloc] init];
	}
}

- (void)parser:(NSXMLParser *)parser didEndElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName{
	
	//NSLog(@"ended element: %@", elementName);
	//if ([elementName isEqualToString:@"result"]) {
	//do i do anything with it?
	//}
	
}

- (void)parser:(NSXMLParser *)parser foundCharacters:(NSString *)string{
	//NSLog(@"found characters: %@", string);
	// save the characters for the current item...
	if ([currentElement isEqualToString:@"result"]) {
		[currentResult appendString:string];
	}
}

- (void)parserDidEndDocument:(NSXMLParser *)parser {
	
	//[activityIndicator stopAnimating];
	//[activityIndicator removeFromSuperview];
	
	NSLog(@"all done! RESULT: %@", currentResult);
	if ([currentResult isEqualToString:@"true"]) {
		//TODO: success output
	}
}


- (void)dealloc {
	[resetButton release];
	[closeButton release];
	[rootController release];
	[emailAddressLabel release];
	[pushSwitch release];
	[notifyInSwitch release];
	[notifyOutSwitch release];
	[loadingView release];
    [super dealloc];
}


@end
