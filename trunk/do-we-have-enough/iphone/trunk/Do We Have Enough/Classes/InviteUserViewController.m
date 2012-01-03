//
//  InviteUserViewController.m
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 11/23/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import "InviteUserViewController.h"
#import "LoadingView.h"
#import "SettingsTracker.h"


@implementation InviteUserViewController
@synthesize groupSwitch;
@synthesize emailAddress;
@synthesize event_id;
@synthesize loadingView;


#pragma mark Address Book Methods
- (IBAction)showActionSheet:(id)sender {
	UIActionSheet *actionSheet = [[UIActionSheet alloc] initWithTitle:@"Select a method to add an email address." delegate:self cancelButtonTitle:@"Cancel" destructiveButtonTitle:nil otherButtonTitles:@"Select from Address Book", @"Enter Email Address", nil];
	[actionSheet showInView:self.view];
	[actionSheet release];
}
- (IBAction)showPicker:(id)sender {
    ABPeoplePickerNavigationController *picker =
	[[ABPeoplePickerNavigationController alloc] init];
    picker.peoplePickerDelegate = self;
	
    [self presentModalViewController:picker animated:YES];
    [picker release];
}
- (void)peoplePickerNavigationControllerDidCancel:
(ABPeoplePickerNavigationController *)peoplePicker {
    [self dismissModalViewControllerAnimated:YES];
}


- (BOOL)peoplePickerNavigationController:
(ABPeoplePickerNavigationController *)peoplePicker
      shouldContinueAfterSelectingPerson:(ABRecordRef)person {
    return YES;
}
- (BOOL)peoplePickerNavigationController:
(ABPeoplePickerNavigationController *)peoplePicker
      shouldContinueAfterSelectingPerson:(ABRecordRef)person
                                property:(ABPropertyID)property
                              identifier:(ABMultiValueIdentifier)identifier{
	if( property == kABPersonEmailProperty ) {
		NSString* name = (NSString *)ABRecordCopyValue(person,
													   kABPersonFirstNameProperty);
		//self.firstName.text = name;
		[name release];
		
		name = (NSString *)ABRecordCopyValue(person, kABPersonLastNameProperty);
		//self.lastName.text = name;
		[name release];
		
		NSString *emailRef = (NSString *)ABMultiValueCopyValueAtIndex(ABRecordCopyValue(person, kABPersonEmailProperty), identifier);
		NSLog( @"User selected email address = %@", emailRef );
		self.emailAddress.text = emailRef;
		[emailRef release];
		[self dismissModalViewControllerAnimated:YES];
		return NO;
	} else {
		return YES;
	}
	
    
}
#pragma mark Action Sheet Methods
- (void)actionSheet:(UIActionSheet *)actionSheet
didDismissWithButtonIndex:(NSInteger)buttonIndex
{
	if (buttonIndex == [actionSheet firstOtherButtonIndex])
	{
		[self showPicker:actionSheet];
	} else if (buttonIndex == [actionSheet cancelButtonIndex]) {
		NSLog(@"cancelled");
	} else {
		[self addEmailPressed:actionSheet];
	}
	
}
-(IBAction)addEmailPressed:(id)sender {
	[emailAddress becomeFirstResponder];
}

#pragma mark Save Cancel methods

-(IBAction)cancel:(id)sender {
	[self.navigationController popViewControllerAnimated:YES];
}
-(IBAction)save:(id)sender {
	
	NSString *email = [emailAddress.text lowercaseString];
    NSString *emailRegEx =
    @"(?:[a-z0-9!#$%\\&'*+/=?\\^_`{|}~-]+(?:\\.[a-z0-9!#$%\\&'*+/=?\\^_`{|}"
    @"~-]+)*|\"(?:[\\x01-\\x08\\x0b\\x0c\\x0e-\\x1f\\x21\\x23-\\x5b\\x5d-\\"
    @"x7f]|\\\\[\\x01-\\x09\\x0b\\x0c\\x0e-\\x7f])*\")@(?:(?:[a-z0-9](?:[a-"
    @"z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\\[(?:(?:25[0-5"
    @"]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-"
    @"9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\\x01-\\x08\\x0b\\x0c\\x0e-\\x1f\\x21"
    @"-\\x5a\\x53-\\x7f]|\\\\[\\x01-\\x09\\x0b\\x0c\\x0e-\\x7f])+)\\])";
    
    NSPredicate *regExPredicate = [NSPredicate predicateWithFormat:@"SELF MATCHES %@", emailRegEx];
    BOOL myStringMatchesRegEx = [regExPredicate evaluateWithObject:email];
	if (myStringMatchesRegEx) {
		[emailAddress resignFirstResponder];
		LoadingView *myLoadingView = [LoadingView loadingViewInView:[self.view.window.subviews objectAtIndex:0]];
		loadingView = myLoadingView;
		NSString * path = [[NSString alloc] initWithFormat:@"http://%@.dowehaveenough.com/devices/invite_user.xml", [[[NSBundle mainBundle] infoDictionary] valueForKey:@"WEB_ENVIRONMENT"]];
		
		[self retrieveXMLFileAtURL:path];
		[path release];
	} else {
		UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Incorrect Email" message:@"Please check the format of the email address you entered." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
		[alert show];
		[alert release];
	}
	
}


- (void)viewDidLoad {
	UIBarButtonItem *cancelButton = [[UIBarButtonItem alloc]
									 initWithTitle:@"Cancel"
									 style:UIBarButtonItemStyleBordered
									 target:self
									 action:@selector(cancel:)];
	self.navigationItem.leftBarButtonItem = cancelButton;
	[cancelButton release];
	UIBarButtonItem *doneButton = [[UIBarButtonItem alloc]
								   initWithBarButtonSystemItem:UIBarButtonSystemItemSave
								   target:self action:@selector(save:)];
	self.navigationItem.rightBarButtonItem = doneButton;
	[doneButton release];
	self.title = @"Invite Someone";
	self.view.backgroundColor = [UIColor clearColor];
    [super viewDidLoad];
}
-(void)viewDidAppear:(BOOL)animated {
	if ([emailAddress.text isEqualToString:@""]) [self showActionSheet:self];
	[super viewDidAppear:animated];
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

- (void)viewDidUnload {
	// Release any retained subviews of the main view.
	// e.g. self.myOutlet = nil;
}

#pragma mark POST methods
- (void)retrieveXMLFileAtURL:(NSString *)URL {
	UIDevice *device = [UIDevice currentDevice];
	NSString *uniqueIdentifier = [device uniqueIdentifier];
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	NSString *postString = [NSString stringWithFormat:@"email_address=%@&device_id=%@&event_id=%@&invite_email_address=%@&add_to_group=%d", settings.emailAddress, uniqueIdentifier, event_id, emailAddress.text, [groupSwitch isOn]];
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
	
	NSLog(@"all done: %@", currentResult);
	if ([currentResult isEqualToString:@"true"]) {
		//[self.parentController updateMyStatus:myStatus :myGuests];
		[self.navigationController popViewControllerAnimated:YES];
	} else {
		UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Error" message:currentResult delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
		[alert show];
		[alert release];
	}

}


- (void)dealloc {
	[groupSwitch release];
	[emailAddress release];
	[event_id release];
    [super dealloc];
}


@end
