//
//  EventDetailsViewController.m
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 11/19/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import "EventDetailsViewController.h";
#import "SetUserStatusViewController.h";
#import "SetUserNotifyViewController.h";
#import "EventMemberListViewController.h";
#import "InviteUserViewController.h";
#import "LoadingView.h";
#import "SettingsTracker.h";
#import "StringHelper.h";


@implementation EventDetailsViewController
@synthesize eventDetails;
@synthesize memberlist;
@synthesize loadingView;
@synthesize event_id;
/*@synthesize currentName, currentWhen, currentWhere, currentNeed;
@synthesize currentNeed, currentDetails, currentActive;
@synthesize currentCannotInvite, currentCannotBring;
@synthesize currentMemberStatus, currentMemberGuests, currentMemberName;
@synthesize currentStatus, currentNotifyWhen;*/
-(void)startInvite
{
	InviteUserViewController *myController = [[InviteUserViewController alloc] initWithNibName:@"InviteUserViewController" bundle:nil];
	myController.event_id = event_id;
	[self.navigationController pushViewController:myController animated:YES];
	[myController release];
	
}
-(void)updateMyStatus:(int)status :(int)guests
{
	NSLog(@"my status is: %d", status);
	NSString *myStatus = [[NSString alloc] initWithFormat:@"%d", status];
	NSString *myGuests = [[NSString alloc] initWithFormat:@"%d", guests];
	[eventDetails setObject:myGuests forKey:@"your_guests"];
	[eventDetails setObject:myStatus forKey:@"your_status"];
	[myGuests release];
	[myStatus release];
	[self.tableView reloadData];
}
-(void)updateMyNotify:(int)notify
{
	NSString *myNotify = [[NSString alloc] initWithFormat:@"%d", notify];
	[eventDetails setObject:myNotify forKey:@"notify_when"];
	[myNotify release];
	[self.tableView reloadData];
}

-(void)refreshView
{
	
	int myInvite = [[eventDetails objectForKey:@"cannotInvite"] intValue];
	if (myInvite != 1) {
		[self.navigationController setToolbarHidden:NO animated:YES];
		UIBarButtonItem *flexibleSpace = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemFlexibleSpace target:nil action:nil];
		UIBarButtonItem *inviteButton = [[UIBarButtonItem alloc]
									 initWithTitle:@"Invite" 
									 style:UIBarButtonItemStyleBordered 
									 target:self 
											 action:@selector(startInvite)];
		NSArray *items = [[NSArray alloc] initWithObjects:flexibleSpace, inviteButton, nil];
		self.toolbarItems = items;
		[flexibleSpace release];
		[inviteButton release];
		[items release];
	} else [self.navigationController setToolbarHidden:YES animated:YES];
}
-(void)refreshData
{
	[memberlist removeAllObjects];
	[eventDetails removeAllObjects];
	LoadingView *myLoadingView = [LoadingView loadingViewInView:[self.view.window.subviews objectAtIndex:0]];
	loadingView = myLoadingView;
	NSString * path = [[NSString alloc] initWithFormat:@"http://%@.dowehaveenough.com/devices/get_event.xml", [[[NSBundle mainBundle] infoDictionary] valueForKey:@"WEB_ENVIRONMENT"]];
	
	[self retrieveXMLFileAtURL:path];
	[path release];
}


- (void)viewDidLoad {
	memberlist = [[NSMutableArray alloc] init];
	eventDetails = [[NSMutableDictionary alloc] init];
    [[self navigationController] setNavigationBarHidden:NO animated:YES];
	NSString *myTitle = [[NSString alloc] initWithString:@"Event Details"];
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
	if ([eventDetails count] == 0) {
		[self refreshData];
	}
    [super viewDidAppear:animated];
}

/*
- (void)viewWillAppear:(BOOL)animated {
    [super viewWillAppear:animated];
}
*/
/*
- (void)viewDidAppear:(BOOL)animated {
    [super viewDidAppear:animated];
}
*/
/*
- (void)viewWillDisappear:(BOOL)animated {
	[super viewWillDisappear:animated];
}
*/
/*
- (void)viewDidDisappear:(BOOL)animated {
	[super viewDidDisappear:animated];
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


#pragma mark Table view methods

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
	
	if ([eventDetails count] < 1) return 1;
	else {
		NSString *label = [eventDetails objectForKey:@"details"];
		NSLog(@"label length is: %d", [label length]);
		if ([label length] <= 1) return 3;
		else return 4;
	}
}
/*- (NSString *)tableView:(UITableView *)tableView
titleForHeaderInSection:(NSInteger)section
{
	if ([eventDetails count] < 1) return @"Loading...";
    switch (section)
    {
		case EventStatusSection: return @"Event Status";
        case EventDetailsSection2:  return @"Event Info";
        case InvitePeopleSection2: return @"The Peeps";
		case DetailsSection: return @"Extra Details";
    }
    
    return nil;
}*/
- (UIView *)tableView:(UITableView *)tableView viewForHeaderInSection:(NSInteger)section
{
	// create the parent view that will hold header Label
	UIView* customView = [[[UIView alloc] initWithFrame:CGRectMake(10,0,320,44)] autorelease];
	
	// create image object
	UIImage *myImage = nil;
	if ([eventDetails count] < 1) myImage = [UIImage imageNamed:@"title_loading.png"];
	else {
		switch (section)
		{
			case EventStatusSection:
			{
				myImage = [UIImage imageNamed:@"title_event_status.png"];
				break;
			}
			case EventDetailsSection2: 
				myImage = [UIImage imageNamed:@"title_the_event.png"];
				break;
			case InvitePeopleSection2: 
				myImage = [UIImage imageNamed:@"title_the_peeps.png"];
				break;
			case DetailsSection: 
				myImage = [UIImage imageNamed:@"title_the_details.png"];
				break;
		}
	}
	
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


// Customize the number of rows in the table view.
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
	if ([eventDetails count] < 1) return 0;
    switch (section)
    {
        case EventStatusSection: return 2;
		case EventDetailsSection2:  return 3;
        case InvitePeopleSection2: return 3;
		case DetailsSection: return 1;
    }
    
    return 0;
}
- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath  {  
	NSUInteger section = [indexPath section];
	if (section == DetailsSection) {
		NSString *label = [eventDetails objectForKey:@"details"];
		return [label RAD_textHeightForSystemFontOfSize:12] + 20.0;
	} else {
		NSString *label = @"nothing";
		return [label RAD_textHeightForSystemFontOfSize:12] + 20.0;
	}
} 


// Customize the appearance of table view cells.
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    NSUInteger section = [indexPath section];
	NSUInteger row = [indexPath row];
    static NSString *cellBasic = @"Cell";
	static NSString *cellDisclose = @"CellDisclose";
	static NSString *cellExpand = @"CellExpand";
    
    UITableViewCell *cell = nil;
    switch (section) {
		case EventStatusSection:
		{
			switch (row) {
				case EventStatusCell:
				{
					cell = [tableView dequeueReusableCellWithIdentifier:cellBasic];
					if (cell == nil) {
						cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue2 reuseIdentifier:cellBasic] autorelease];
					}
					cell.detailTextLabel.adjustsFontSizeToFitWidth = YES;
					
					int newActive = [[eventDetails objectForKey:@"active"] intValue];
					if (newActive == 1) 
						cell.detailTextLabel.text = @"NEEDS MORE";
					else if (newActive == 3) 
						cell.detailTextLabel.text = @"IT'S ON!";
					else if (newActive == 2) 
						cell.detailTextLabel.text = @"NEEDS MORE!";
					else if (newActive == 0) 
						cell.detailTextLabel.text = @"OFF";
					cell.textLabel.text = @"Status:";
					break;
				}
				case YourStatusCell:
				{
					cell = [tableView dequeueReusableCellWithIdentifier:cellDisclose];
					if (cell == nil) {
						cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue2 reuseIdentifier:cellDisclose] autorelease];
					}
					cell.detailTextLabel.adjustsFontSizeToFitWidth = YES;
					cell.textLabel.text = @"You Are:";
					int myStatus = [[eventDetails objectForKey:@"your_status"] intValue];
					if (myStatus == 0) 
						cell.detailTextLabel.text = @"< not yet set >";
					else if (myStatus == 1) {
						int myGuests = [[eventDetails objectForKey:@"your_guests"] intValue];
						if (myGuests == 0) cell.detailTextLabel.text = @"IN";
						else cell.detailTextLabel.text = [NSString	stringWithFormat:@"IN + %d", myGuests];
					} else if (myStatus == 2)
						cell.detailTextLabel.text = @"OUT";
					else if (myStatus == 3)
						cell.detailTextLabel.text = @"50/50";
					
					cell.accessoryType = UITableViewCellAccessoryDisclosureIndicator;
				}
				default:
					break;
			}
			
			break;
		}
		case EventDetailsSection2:
		{
			cell = [tableView dequeueReusableCellWithIdentifier:cellBasic];
			if (cell == nil) {
				cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue2 reuseIdentifier:cellBasic] autorelease];
			}
			cell.detailTextLabel.adjustsFontSizeToFitWidth = YES;
			switch (row) {
				case EventNameCell2:
				{
					NSString *eventName = [[NSString alloc] initWithFormat:@"%@", [eventDetails objectForKey:@"name"]];
					cell.detailTextLabel.text = eventName;
					[eventName release];
					cell.textLabel.text = @"What:";
					break;
				}
				case EventTimeCell2:
				{
					NSString *eventName = [[NSString alloc] initWithFormat:@"%@", [eventDetails objectForKey:@"when"]];
					cell.detailTextLabel.text = eventName;
					[eventName release];
					cell.textLabel.text = @"When:";
					break;
				}
				case EventLocationCell2:
				{
					NSString *eventName = [[NSString alloc] initWithFormat:@"%@", [eventDetails objectForKey:@"where"]];
					cell.detailTextLabel.text = eventName;
					[eventName release];
					cell.textLabel.text = @"Where:";
					break;
				}
				default:
					break;
			}
			break;
		}
		case InvitePeopleSection2:
		{
			
			switch (row) {
				case PeopleStatusCell:
				{
					cell = [tableView dequeueReusableCellWithIdentifier:cellDisclose];
					if (cell == nil) {
						cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue2 reuseIdentifier:cellDisclose] autorelease];
					}
					cell.detailTextLabel.adjustsFontSizeToFitWidth = YES;
					cell.accessoryType = UITableViewCellAccessoryDisclosureIndicator;
					cell.textLabel.text = @"We Got:";
					int members_in = 0;
					int members_out= 0;
					int members_50 = 0;
					for (int i=0; i<[memberlist count]; i++) {
						NSMutableDictionary *myMember = [memberlist objectAtIndex:i];
						NSMutableString *myStatus = [[NSString alloc] initWithString:[myMember objectForKey:@"status"]];
						int myStatusInt = [myStatus intValue];
						int myGuestsInt = [[myMember objectForKey:@"guests"] intValue];
						if (myStatusInt == 1) members_in = members_in + 1 + myGuestsInt;
						else if (myStatusInt == 2) members_out++;
						else if (myStatusInt == 3) members_50++;
						[myStatus release];
					}
					NSString *myMsg = [[NSString alloc] initWithFormat:@"%d IN / %d OUT / %d 50-50", members_in, members_out, members_50];
					cell.detailTextLabel.text = myMsg;
					[myMsg release];
					break;
				}
				case NeedStatusCell:
				{
					cell = [tableView dequeueReusableCellWithIdentifier:cellBasic];
					if (cell == nil) {
						cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue2 reuseIdentifier:cellBasic] autorelease];
					}
					cell.textLabel.text = @"We Need:";
					NSString *myMsg = [[NSString alloc] initWithFormat:@"%@ People", [eventDetails objectForKey:@"need"]];
					cell.detailTextLabel.text = myMsg;
					[myMsg release];
					break;
				}
				case NotifyMeCell:
				{
					cell = [tableView dequeueReusableCellWithIdentifier:cellDisclose];
					if (cell == nil) {
						cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue2 reuseIdentifier:cellDisclose] autorelease];
					}
					cell.detailTextLabel.adjustsFontSizeToFitWidth = YES;
					cell.accessoryType = UITableViewCellAccessoryDisclosureIndicator;
					cell.textLabel.text = @"Notify Me:";
					int notifyMe = [[eventDetails objectForKey:@"notify_when"] intValue];
					NSString *myMsg = [[NSString alloc] init];
					if (notifyMe == 0) {
						myMsg = [[NSString alloc] initWithFormat:@"< not yet set >"];
						cell.detailTextLabel.textColor = [UIColor grayColor];
					} else {
						myMsg = [[NSString alloc] initWithFormat:@"When we reach %d", notifyMe];
						cell.detailTextLabel.textColor = [UIColor blackColor];
					}
					cell.detailTextLabel.text = myMsg;
					[myMsg release];
					break;
				}
				default:
					break;
			}
			break;
		}
		case DetailsSection:
		{
			switch (row) {
				case EventDetailsCell2:
				{
					cell = [tableView dequeueReusableCellWithIdentifier:cellExpand];
					if (cell == nil) {
						cell = [[[UITableViewCell alloc] initWithFrame:CGRectZero reuseIdentifier:cellExpand] autorelease];
					}
					NSString *label = [eventDetails objectForKey:@"details"];
					if ([[cell.contentView subviews] count] > 0) {
						id view = [[cell.contentView subviews] objectAtIndex:0];
						UILabel *labelToSize = view;
						[label RAD_resizeLabel:labelToSize WithSystemFontOfSize:12];
					} else {
						UILabel *cellLabel;
						cellLabel = [label RAD_newSizedCellLabelWithSystemFontOfSize:12];
						[cell.contentView addSubview:cellLabel];
						[cellLabel release];
					}
					break;
				}
				default:
					break;
			}
			break;
		}
	}
    return cell;
}


- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
    NSUInteger section = [indexPath section];
	NSUInteger row = [indexPath row];
	if (section == EventStatusSection && row == YourStatusCell) {
		SetUserStatusViewController *myController = [[SetUserStatusViewController alloc] initWithNibName:@"SetUserStatusViewController" bundle:nil];
		[myController setMyStatus:[[eventDetails objectForKey:@"your_status"] intValue]];
		myController.myGuests = [[eventDetails objectForKey:@"your_guests"] intValue];
		myController.cannotBringGuests = [[eventDetails objectForKey:@"cannotBring"] intValue];
		myController.parentController = self;
		myController.event_id = event_id;
		[self.navigationController pushViewController:myController animated:YES];
		[myController release];
	}
	if (section == InvitePeopleSection2 && row == PeopleStatusCell) {
		EventMemberListViewController *myController = [[EventMemberListViewController alloc] initWithStyle:UITableViewStylePlain];
		myController.memberlist = memberlist;
		[self.navigationController pushViewController:myController animated:YES];
		[myController release];
	}
	if (section == InvitePeopleSection2 && row == NotifyMeCell) {
		SetUserNotifyViewController *myController = [[SetUserNotifyViewController alloc] initWithNibName:@"SetUserNotifyViewController" bundle:nil];
		myController.myNotify = [[eventDetails objectForKey:@"notify_when"] intValue];
		myController.parentController = self;
		myController.event_id = event_id;
		[self.navigationController pushViewController:myController animated:YES];
		[myController release];
	}
	
	[tableView deselectRowAtIndexPath:indexPath animated:YES];
	
	
}


/*
// Override to support conditional editing of the table view.
- (BOOL)tableView:(UITableView *)tableView canEditRowAtIndexPath:(NSIndexPath *)indexPath {
    // Return NO if you do not want the specified item to be editable.
    return YES;
}
*/


/*
// Override to support editing the table view.
- (void)tableView:(UITableView *)tableView commitEditingStyle:(UITableViewCellEditingStyle)editingStyle forRowAtIndexPath:(NSIndexPath *)indexPath {
    
    if (editingStyle == UITableViewCellEditingStyleDelete) {
        // Delete the row from the data source
        [tableView deleteRowsAtIndexPaths:[NSArray arrayWithObject:indexPath] withRowAnimation:YES];
    }   
    else if (editingStyle == UITableViewCellEditingStyleInsert) {
        // Create a new instance of the appropriate class, insert it into the array, and add a new row to the table view
    }   
}
*/


/*
// Override to support rearranging the table view.
- (void)tableView:(UITableView *)tableView moveRowAtIndexPath:(NSIndexPath *)fromIndexPath toIndexPath:(NSIndexPath *)toIndexPath {
}
*/


/*
// Override to support conditional rearranging of the table view.
- (BOOL)tableView:(UITableView *)tableView canMoveRowAtIndexPath:(NSIndexPath *)indexPath {
    // Return NO if you do not want the item to be re-orderable.
    return YES;
}
*/

#pragma mark POST methods
- (void)retrieveXMLFileAtURL:(NSString *)URL {
	UIDevice *device = [UIDevice currentDevice];
	NSString *uniqueIdentifier = [device uniqueIdentifier];
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	NSString *postString = [NSString stringWithFormat:@"email_address=%@&device_id=%@&event_id=%@", settings.emailAddress, uniqueIdentifier, event_id];
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
	
	if ([elementName isEqualToString:@"event_data"]) {
		// clear out our story item caches...
		//item = [[NSMutableDictionary alloc] init];
		currentName = [[NSMutableString alloc] init];
		currentWhen = [[NSMutableString alloc] init];
		currentWhere = [[NSMutableString alloc] init];
		currentNeed = [[NSMutableString alloc] init];
		currentDetails = [[NSMutableString alloc] init];
		currentActive = [[NSMutableString alloc] init];
		currentCannotInvite = [[NSMutableString alloc] init];
		currentCannotBring = [[NSMutableString alloc] init];
	} else if ([elementName isEqualToString:@"event_member"]) {
		memberItem = [[NSMutableDictionary alloc] init];
		currentMemberName = [[NSMutableString alloc] init];
		currentMemberGuests = [[NSMutableString alloc] init];
		currentMemberStatus = [[NSMutableString alloc] init];
	} else if ([elementName isEqualToString:@"event_your_status"]) {
		currentStatus = [[NSMutableString alloc] init];
	} else if ([elementName isEqualToString:@"notify_when"]) {
		currentNotifyWhen = [[NSMutableString alloc] init];
	} else if ([elementName isEqualToString:@"event_your_guests"]) {
		currentGuests = [[NSMutableString alloc] init];
	}
}

- (void)parser:(NSXMLParser *)parser didEndElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName{
	
	//NSLog(@"ended element: %@", elementName);
	if ([elementName isEqualToString:@"event_data"]) {
		// save values to an item, then store that item into the array...
		[eventDetails setObject:currentName	forKey:@"name"];
		[eventDetails setObject:currentWhen	forKey:@"when"];
		[eventDetails setObject:currentWhere	forKey:@"where"];
		[eventDetails setObject:currentNeed	forKey:@"need"];
		[eventDetails setObject:currentDetails	forKey:@"details"];
		[eventDetails setObject:currentActive	forKey:@"active"];
		[eventDetails setObject:currentCannotInvite	forKey:@"cannotInvite"];
		[eventDetails setObject:currentCannotBring	forKey:@"cannotBring"];
		
		//[eventlist addObject:[item copy]];
		NSLog(@"adding event name: %@", currentName);
		NSLog(@"adding event details: %@", currentDetails);
	} else if ([elementName isEqualToString:@"event_member"]) {
		// save values to an item, then store that item into the array...
		[memberItem setObject:currentMemberName	forKey:@"name"];
		[memberItem setObject:currentMemberStatus	forKey:@"status"];
		[memberItem setObject:currentMemberGuests	forKey:@"guests"];
		
		[memberlist addObject:[memberItem copy]];
		NSLog(@"adding member: %@", currentMemberName);
	} else if ([elementName isEqualToString:@"event_your_status"]) {
		[eventDetails setObject:currentStatus forKey:@"your_status"];
		NSLog(@"adding event your status: %@", currentStatus);
	} else if ([elementName isEqualToString:@"notify_when"]) {
		[eventDetails setObject:currentNotifyWhen forKey:@"notify_when"];
	} else if ([elementName isEqualToString:@"event_your_guests"]) {
		[eventDetails setObject:currentGuests forKey:@"your_guests"];
	}

}

- (void)parser:(NSXMLParser *)parser foundCharacters:(NSString *)string{
	//NSLog(@"found characters: %@", string);
	// save the characters for the current item...
	if ([currentElement isEqualToString:@"event_name"]) {
		[currentName appendString:string];
	} else if ([currentElement isEqualToString:@"event_when"]) {
		[currentWhen appendString:string];
	} else if ([currentElement isEqualToString:@"event_where"]) {
		[currentWhere appendString:string];
	} else if ([currentElement isEqualToString:@"event_need"]) {
		[currentNeed appendString:string];
	} else if ([currentElement isEqualToString:@"event_details"]) {
		[currentDetails appendString:string];
	} else if ([currentElement isEqualToString:@"event_active"]) {
		[currentActive appendString:string];
	} else if ([currentElement isEqualToString:@"event_cannot_invite_others"]) {
		[currentCannotInvite appendString:string];
	} else if ([currentElement isEqualToString:@"event_cannot_bring_guests"]) {
		[currentCannotBring appendString:string];
	} else if ([currentElement isEqualToString:@"status"]) {
		[currentMemberStatus appendString:string];
	} else if ([currentElement isEqualToString:@"guests"]) {
		[currentMemberGuests appendString:string];
	} else if ([currentElement isEqualToString:@"name"]) {
		[currentMemberName appendString:string];
	} else if ([currentElement isEqualToString:@"event_your_status"]) {
		[currentStatus appendString:string];
	} else if ([currentElement isEqualToString:@"notify_when"]) {
		[currentNotifyWhen appendString:string];
	} else if ([currentElement isEqualToString:@"event_your_guests"]) {
		[currentGuests appendString:string];
	}
}

- (void)parserDidEndDocument:(NSXMLParser *)parser {
	
	//[activityIndicator stopAnimating];
	//[activityIndicator removeFromSuperview];
	
	//NSLog(@"all done!");
	//NSLog(@"members array has %d items", [memberlist count]);
	self.title = [eventDetails objectForKey:@"name"];
	[self.tableView reloadData];
	[self refreshView];
}



- (void)dealloc {
	[eventDetails release];
	[memberlist release];
	[event_id release];
    [super dealloc];
}


@end

