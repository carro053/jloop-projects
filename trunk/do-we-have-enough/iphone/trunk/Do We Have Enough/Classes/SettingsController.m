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
#import "TestFlight.h"


@implementation SettingsController
@synthesize resetButton;
@synthesize closeButton;
@synthesize rootController;
@synthesize emailAddressLabel;
@synthesize notifyInSwitch;
@synthesize notifyOutSwitch;
@synthesize notifyEventChangeSwitch;
@synthesize appNotifyInSwitch;
@synthesize appNotifyOutSwitch;
@synthesize appNotifyEventChangeSwitch;
@synthesize scroller;
@synthesize userNameField;
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
-(IBAction)toggleInSwitch:(id)sender
{
    [TestFlight passCheckpoint:@"SETTINGS TOGGLE IN"];
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
    [TestFlight passCheckpoint:@"SETTINGS TOGGLE OUT"];
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
-(IBAction)toggleEventChangeSwitch:(id)sender
{
    [TestFlight passCheckpoint:@"SETTINGS TOGGLE EVENT CHANGE"];
	LoadingView *myLoadingView = [LoadingView loadingViewInView:[self.view.window.subviews objectAtIndex:0]];
	loadingView = myLoadingView;
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	NSString *myNotify = [[NSString alloc] initWithFormat:@"%d", [sender isOn]];
	[settings saveNotifyEventChange:myNotify];
	[myNotify release];
	[settings release];
	[self saveNotifications];
}
-(IBAction)toggleAppInSwitch:(id)sender
{
    [TestFlight passCheckpoint:@"SETTINGS TOGGLE APP IN"];
	LoadingView *myLoadingView = [LoadingView loadingViewInView:[self.view.window.subviews objectAtIndex:0]];
	loadingView = myLoadingView;
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	NSString *myNotify = [[NSString alloc] initWithFormat:@"%d", [sender isOn]];
	[settings saveAppNotifyIn:myNotify];
	[myNotify release];
	[settings release];
	[self saveNotifications];
}
-(IBAction)toggleAppOutSwitch:(id)sender
{
    [TestFlight passCheckpoint:@"SETTINGS TOGGLE APP OUT"];
	LoadingView *myLoadingView = [LoadingView loadingViewInView:[self.view.window.subviews objectAtIndex:0]];
	loadingView = myLoadingView;
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	NSString *myNotify = [[NSString alloc] initWithFormat:@"%d", [sender isOn]];
	[settings saveAppNotifyOut:myNotify];
	[myNotify release];
	[settings release];
	[self saveNotifications];
}
-(IBAction)toggleAppEventChangeSwitch:(id)sender
{
    [TestFlight passCheckpoint:@"SETTINGS TOGGLE APP EVENT CHANGE"];
	LoadingView *myLoadingView = [LoadingView loadingViewInView:[self.view.window.subviews objectAtIndex:0]];
	loadingView = myLoadingView;
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	NSString *myNotify = [[NSString alloc] initWithFormat:@"%d", [sender isOn]];
	[settings saveAppNotifyEventChange:myNotify];
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
		resetButton.enabled = NO;
		notifyInSwitch.enabled = NO;
		notifyOutSwitch.enabled = NO;
		notifyEventChangeSwitch.enabled = NO;
		appNotifyInSwitch.enabled = NO;
		appNotifyOutSwitch.enabled = NO;
		appNotifyEventChangeSwitch.enabled = NO;
	} else {
		emailAddressLabel.text = settings.emailAddress;
		resetButton.enabled = YES;
		notifyInSwitch.enabled = YES;
		notifyOutSwitch.enabled = YES;
		notifyEventChangeSwitch.enabled = YES;
		appNotifyInSwitch.enabled = YES;
		appNotifyOutSwitch.enabled = YES;
		appNotifyEventChangeSwitch.enabled = YES;
        userNameField.text = [settings.userName stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]];
		[notifyInSwitch setOn:[settings.notifyIn intValue]];
		[notifyOutSwitch setOn:[settings.notifyOut intValue]];
		[notifyEventChangeSwitch setOn:[settings.notifyEventChange intValue]];
		[appNotifyInSwitch setOn:[settings.appNotifyIn intValue]];
		[appNotifyOutSwitch setOn:[settings.appNotifyOut intValue]];
		[appNotifyEventChangeSwitch setOn:[settings.appNotifyEventChange intValue]];
	}
	//emailAddressLabel.text = settings.emailAddress;
	[settings release];
	
}

- (BOOL)textFieldShouldReturn:(UITextField *)theTextField {
    if (theTextField == self.userNameField) {
        [theTextField resignFirstResponder];
        [self saveName:theTextField.text];
    }
    return YES;
}

- (IBAction)dismissKeyboard:(id)sender {
    if([userNameField isFirstResponder])
    {
        [userNameField resignFirstResponder];
        [self saveName:userNameField.text];
    }
}
    
-(void) saveName:(NSString *)theName {
    [TestFlight passCheckpoint:@"SETTINGS SAVE NAME"];
    LoadingView *myLoadingView = [LoadingView loadingViewInView:[self.view.window.subviews objectAtIndex:0]];
    loadingView = myLoadingView;
    SettingsTracker *settings = [[SettingsTracker alloc] init];
    [settings initData];
    [settings saveUserName:theName];
    [settings release];
    [self saveNotifications];
}


// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad {
	//[self setStage];
    [TestFlight passCheckpoint:@"SETTINGS VIEW"];
    [super viewDidLoad];
    
    [scroller setScrollEnabled:YES];
    [scroller setContentSize:CGSizeMake(320, 518)];
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
    [userNameField release];
    userNameField = nil;
	// Release any retained subviews of the main view.
	// e.g. self.myOutlet = nil;
}
#pragma mark POST methods
- (void)saveNotifications {
    [TestFlight passCheckpoint:@"SETTINGS SAVE NOTIFICATIONS"];
	NSString *URL = [[NSString alloc] initWithFormat:@"http://%@.dowehaveenough.com/devices/save_user.xml", [[[NSBundle mainBundle] infoDictionary] valueForKey:@"WEB_ENVIRONMENT"]];
	UIDevice *device = [UIDevice currentDevice];
	NSString *uniqueIdentifier = [device uniqueIdentifier];
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	NSString *postString = [NSString stringWithFormat:@"email_address=%@&device_id=%@&notify_in=%@&notify_out=%@&notify_event_change=%@&app_notify_in=%@&app_notify_out=%@&app_notify_event_change=%@&name=%@", settings.emailAddress, uniqueIdentifier, settings.notifyIn, settings.notifyOut, settings.notifyEventChange, settings.appNotifyIn, settings.appNotifyOut, settings.appNotifyEventChange,settings.userName];
    NSLog(@"%@",postString);
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
    [TestFlight passCheckpoint:@"SETTINGS RESET EMAIL"];
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
	[notifyInSwitch release];
	[notifyOutSwitch release];
	[notifyEventChangeSwitch release];
	[appNotifyInSwitch release];
	[appNotifyOutSwitch release];
	[appNotifyEventChangeSwitch release];
	[loadingView release];
    [userNameField release];
    [super dealloc];
}


@end
