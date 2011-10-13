//
//  EventListViewController.m
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 10/27/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import "EventListViewController.h"
#import "EventDetailsViewController.h"
#import "EventListItem.h"
#import "LoadingView.h"
#import "SettingsTracker.h"


@implementation EventListViewController
@synthesize eventlist;
@synthesize loadingView;

-(void)refreshData
{
	[eventlist removeAllObjects];
	LoadingView *myLoadingView = [LoadingView loadingViewInView:[self.view.window.subviews objectAtIndex:0]];
	loadingView = myLoadingView;
	NSString * path = [[NSString alloc] initWithFormat:@"http://%@.dowehaveenough.com/devices/get_event_list.xml", [[[NSBundle mainBundle] infoDictionary] valueForKey:@"WEB_ENVIRONMENT"]];
	
	[self retrieveXMLFileAtURL:path];
	[path release];
}

- (void)viewDidLoad {
	
	eventlist = [[NSMutableArray alloc] init];
	
	
	/////////
	[[self navigationController] setNavigationBarHidden:NO animated:YES];
	NSString *myTitle = [[NSString alloc] initWithString:@"Event List"];
	UIBarButtonItem *backButton = [[UIBarButtonItem alloc]
								   initWithTitle:myTitle
								   style:UIBarButtonItemStyleBordered
								   target:self
								   action:@selector(cancel:)];
	self.navigationItem.backBarButtonItem = backButton;
	UIBarButtonItem *refreshButton = [[UIBarButtonItem alloc] 
								   initWithBarButtonSystemItem:UIBarButtonSystemItemRefresh 
								   target:self 
									  action:@selector(refreshData)];
	self.navigationItem.rightBarButtonItem = refreshButton;
	self.title = myTitle;
	[backButton release];
	[refreshButton release];
	
	/*int r, g, b;
	 b = 205;
	 g = 155;
	 r = 39;
	 self.tableView.backgroundColor = [UIColor colorWithRed:r/255.0f green:g/255.0f blue:b/255.0f alpha:1.0];*/
	self.tableView.backgroundColor = [UIColor clearColor];
    [super viewDidLoad];
}



- (void)viewDidAppear:(BOOL)animated {
	if ([eventlist count] == 0) {
		[self refreshData];
	}
	[self.navigationController setToolbarHidden:YES animated:YES];
    [super viewDidAppear:animated];
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


#pragma mark Table view methods

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
    if ([self.eventlist count] > 3) return 2;
	else return 1;
}


// Customize the number of rows in the table view.
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
	if ([self.eventlist count] > 3) {
		if (section == 0) return 3;
		else return ([self.eventlist count] - 3);
	} else {
		return [self.eventlist count];
	}
}

/*- (NSString *)tableView:(UITableView *)tableView
titleForHeaderInSection:(NSInteger)section
{
    return @"Event List";
}*/
- (UIView *)tableView:(UITableView *)tableView viewForHeaderInSection:(NSInteger)section
{
	// create the parent view that will hold header Label
	UIView* customView = [[[UIView alloc] initWithFrame:CGRectMake(10,0,320,44)] autorelease];
	UIImage *myImage = nil;
	// create image object
	if (section == 0) myImage = [UIImage imageNamed:@"title_recent_events.png"];
	else myImage = [UIImage imageNamed:@"title_older_events.png"];
	// create the imageView with the image in it
	UIImageView *imageView = [[[UIImageView alloc] initWithImage:myImage] autorelease];
	imageView.frame = CGRectMake(20,10,240,24);
	
	[customView addSubview:imageView];
	
	
	return customView;
}
- (CGFloat)tableView:(UITableView *)tableView heightForHeaderInSection:(NSInteger)section
{
	return 44;
}


// Customize the appearance of table view cells.
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    
    static NSString *CellIdentifier = @"Cell";
    
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleSubtitle reuseIdentifier:CellIdentifier] autorelease];
    }
    NSUInteger section = [indexPath section];
    NSUInteger row = [indexPath row];
	EventListItem *myEvent = nil;
	if (section == 0) myEvent = [self.eventlist objectAtIndex:row];
	else myEvent = [self.eventlist objectAtIndex:(row+3)];
	NSString *cellTitle = [[NSString alloc] initWithFormat:@"%@ - %@", myEvent.eventName, myEvent.eventWhen];
	cell.textLabel.text = cellTitle;
	NSString *onText = [[NSString alloc] initWithFormat:@"Yes! This event is ON with %d!", myEvent.membersIn];
	NSString *offText = [[NSString alloc] initWithString:@"Sorry, this event is OFF"];
	NSString *pendingText = [[NSString alloc] initWithFormat:@"This event NEEDS MORE with %d/%d", myEvent.membersIn, myEvent.eventNeed];
	UIImage *cellImage = nil;
	switch (myEvent.active) {
		case kEventOn:
			cellImage = [UIImage imageNamed:@"icon_thumbs_up.png"];
			cell.detailTextLabel.text = onText;
			break;
		case kEventOff:
			cellImage = [UIImage imageNamed:@"icon_thumbs_down.png"];
			cell.detailTextLabel.text = offText;
			break;
		case (kEventPending || kEventNeedsMore):
			cellImage = [UIImage imageNamed:@"icon_what.png"];
			cell.detailTextLabel.text = pendingText;
			break;
		default:
			break;
	}
	cell.imageView.image = cellImage;
	cell.accessoryType = UITableViewCellAccessoryDisclosureIndicator;
	[cellTitle release];
	[onText release];
	[offText release];
	[pendingText release];
    return cell;
}


- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
    // Navigation logic may go here. Create and push another view controller.
	NSUInteger section = [indexPath section];
	NSUInteger row = [indexPath row];
	EventListItem *myEvent = nil;
	if (section == 0) myEvent = [self.eventlist objectAtIndex:row];
	else myEvent = [self.eventlist objectAtIndex:(row+3)];
	
	EventDetailsViewController *anotherViewController = [[EventDetailsViewController alloc] initWithStyle:UITableViewStyleGrouped];
	anotherViewController.event_id = myEvent.eventID;
	[self.navigationController pushViewController:anotherViewController animated:YES];
	[anotherViewController release];
	[tableView deselectRowAtIndexPath:indexPath animated:YES];
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
	[errorAlert release];
}

- (void)parser:(NSXMLParser *)parser didStartElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName attributes:(NSDictionary *)attributeDict{
	//NSLog(@"found this element: %@", elementName);
	currentElement = [elementName copy];
	
	if ([elementName isEqualToString:@"event"]) {
		// clear out our story item caches...
		//item = [[NSMutableDictionary alloc] init];
		currentName = [[NSMutableString alloc] init];
		currentID = [[NSMutableString alloc] init];
		currentActive = [[NSMutableString alloc] init];
		currentWhen = [[NSMutableString alloc] init];
		currentMembersIn = [[NSMutableString alloc] init];
		currentNeed = [[NSMutableString alloc] init];
	}
}

- (void)parser:(NSXMLParser *)parser didEndElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName{
	
	//NSLog(@"ended element: %@", elementName);
	if ([elementName isEqualToString:@"event"]) {
		// save values to an item, then store that item into the array...
		//[item setObject:currentTitle forKey:@"title"];
		//[item setObject:currentLink forKey:@"link"];
		//[item setObject:currentSummary forKey:@"summary"];
		//[item setObject:currentDate forKey:@"date"];
		EventListItem *event2 = [[EventListItem alloc] init];
		//NSString *myCurrentName = [currentName stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]];
		event2.eventName = [currentName stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]];
		event2.active = [currentActive intValue];
		event2.eventWhen = [currentWhen stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]];;
		event2.membersIn = [currentMembersIn intValue];
		event2.eventNeed = [currentNeed intValue];
		event2.eventID = [currentID stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]];;
		[eventlist addObject:event2];
		[event2 release];
		
		//[eventlist addObject:[item copy]];
		NSLog(@"adding event: %@", currentName);
	}
}

- (void)parser:(NSXMLParser *)parser foundCharacters:(NSString *)string{
	//NSLog(@"found characters: %@", string);
	// save the characters for the current item...
	if ([currentElement isEqualToString:@"id"]) {
		[currentID appendString:string];
	} else if ([currentElement isEqualToString:@"name"]) {
		[currentName appendString:string];
	} else if ([currentElement isEqualToString:@"active"]) {
		[currentActive appendString:string];
	} else if ([currentElement isEqualToString:@"when"]) {
		[currentWhen appendString:string];
	} else if ([currentElement isEqualToString:@"need"]) {
		[currentNeed appendString:string];
	} else if ([currentElement isEqualToString:@"members_in"]) {
		[currentMembersIn appendString:string];
	} 
}

- (void)parserDidEndDocument:(NSXMLParser *)parser {
	
	//[activityIndicator stopAnimating];
	//[activityIndicator removeFromSuperview];
	
	NSLog(@"all done!");
	NSLog(@"events array has %d items", [eventlist count]);
	[self.tableView reloadData];
}



- (void)dealloc {
	[eventlist release];
    [super dealloc];
}


@end

