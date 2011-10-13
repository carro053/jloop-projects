//
//  CheckValidationViewController.m
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 11/18/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import "CheckValidationViewController.h"
#import "SettingsTracker.h"
#import "LoadingView.h"
#import "HomeController.h"

@implementation CheckValidationViewController
@synthesize loadingView;
@synthesize checkButton;
@synthesize restartButton;

-(IBAction)cancel:(id)sender {
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings resetData];
	[settings release];
	[self dismissModalViewControllerAnimated:YES];
}
-(IBAction)checkButtonPressed {
	LoadingView *myLoadingView = [LoadingView loadingViewInView:[self.view.window.subviews objectAtIndex:0]];
	loadingView = myLoadingView;
	NSString * path = [[NSString alloc] initWithFormat:@"http://%@.dowehaveenough.com/devices/check_for_validation.xml", [[[NSBundle mainBundle] infoDictionary] valueForKey:@"WEB_ENVIRONMENT"]];
	
	[self retrieveXMLFileAtURL:path];
	[path release];
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
	NSString * errorString = [NSString stringWithFormat:@"Unable to download event feed from web site (Error code %i )", [parseError code]];
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
	if ([elementName isEqualToString:@"result"]) {
		
		NSLog(@"the result: %@", currentResult);
		SettingsTracker *settings = [[SettingsTracker alloc] init];
		[settings initData];
		if ([currentResult isEqualToString:@"true"]) {
			[settings saveValidation:@"true"];
			[(HomeController *)self.parentViewController dismissModalViewControllerAnimated:YES];
		} else {
			//TODO: add failure alert
		}
		[settings release];
	}
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
	
	NSLog(@"all done!");
	//NSLog(@"events array has %d items", [eventlist count]);
	//[self.tableView reloadData];
}


- (void)dealloc {
	[checkButton release];
	[restartButton release];
    [super dealloc];
}


@end
