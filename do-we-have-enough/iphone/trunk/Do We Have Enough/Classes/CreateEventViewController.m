//
//  CreateEventViewController.m
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 11/16/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import "CreateEventViewController.h"
#import "ChooseGroupController.h"
#import "ChooseUsersController.h"
#import "AddNoteViewController.h"
#import "EventOptionsViewController.h"
#import "ChoosePeopleNeedViewController.h"
#import "EventDetailsViewController.h"
#import "ValidateEmailCreateViewController.h"
#import "RootViewController.h"
#import "CheckValidationViewController.h"
#import "UserGroup.h"
#import "EditableCell.h"
#import "LoadingView.h"
#import "SettingsTracker.h"


@implementation CreateEventViewController
@synthesize grouplist, newGroup, selectedGroupID;
@synthesize eventDetails;
@synthesize inviteOthers, bringGuests, statusEmail, cancelEmail;
@synthesize statusEmailDate, cancelEmailDate;
@synthesize eventNameField = _eventNameField;
@synthesize eventLocationField = _eventLocationField;
@synthesize eventTimeField = _eventTimeField;
@synthesize eventNeed;
@synthesize loadingView;
@synthesize eventName, eventTime, eventLocation;


- (NSTimeInterval)calcTimezoneDiff
{
	NSTimeZone	*nsZoneInfo = nil;	// Timezone of target
	NSTimeZone	*nsZoneLocal = nil;	// Timezone we are in
	
	@try {
		// Get those two time zones
		nsZoneInfo = [NSTimeZone timeZoneWithAbbreviation:@"PDT"];	
		nsZoneLocal = [NSTimeZone localTimeZone];
	}
	@catch (NSException* e) {
		// Log any errors
		NSLog(@"HUDView::calcTimezoneDiff %@", e);
	}
	
	// Return the difference
	return ([nsZoneInfo secondsFromGMT] - [nsZoneLocal secondsFromGMT]);
}
-(IBAction)cancel:(id)sender {
	[self.navigationController popViewControllerAnimated:YES];
}
-(void)startCreate
{
	[self storeEventValues];
	if (eventName == nil || [eventName isEqualToString:@""]) {
		//no event name
		UIAlertView *nameAlert = [[UIAlertView alloc] initWithTitle:@"Whoops!" message:@"You gotta call this event something..." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
		[nameAlert show];
		[nameAlert release];
	} else if (eventTime == nil || [eventTime isEqualToString:@""]) {
		//no event name
		UIAlertView *timeAlert = [[UIAlertView alloc] initWithTitle:@"Whoops!" message:@"People will show up at different times if you don't specify when it is." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
		[timeAlert show];
		[timeAlert release];
	} else if (eventLocation == nil || [eventLocation isEqualToString:@""]) {
		//no event name
		UIAlertView *locationAlert = [[UIAlertView alloc] initWithTitle:@"Whoops!" message:@"Hmmm.... no one can come if they don't know where it is." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
		[locationAlert show];
		[locationAlert release];
	} else if (eventNeed == 0) {
		//no event name
		UIAlertView *needAlert = [[UIAlertView alloc] initWithTitle:@"Whoops!" message:@"How many people do you need for your event?" delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
		[needAlert show];
		[needAlert release];
	} else if ([selectedGroupID isEqualToString:@"0"] && [newGroup.groupMembers count] < 1) {
			UIAlertView *inviteAlert = [[UIAlertView alloc] initWithTitle:@"Whoops!" message:@"You better invite some people or no one will show up." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
			[inviteAlert show];
			[inviteAlert release];
	} else { //MADE IT PAST DATA VALIDATION
		SettingsTracker *settings = [[SettingsTracker alloc] init];
		[settings initData];
		if ([settings.emailAddress isEqualToString:@"false"]) { //DON'T KNOW THE EMAIL ADDY
			ValidateEmailCreateViewController *myController = [[ValidateEmailCreateViewController alloc] initWithNibName:@"ValidateEmailCreateViewController" bundle:nil];
			myController.parentController = self;
			[self presentModalViewController:myController animated:YES];
			[myController release];
		} else {
			//SHOW THE ACTION SHEET
			UIActionSheet *actionSheet = [[UIActionSheet alloc] initWithTitle:@"Invitations will be sent now.  Ready?" delegate:self cancelButtonTitle:@"Cancel" destructiveButtonTitle:@"Create Event" otherButtonTitles:nil];
			[actionSheet showInView:self.view];
			[actionSheet release];
			
		}
		[settings release];
	}
}
-(void)resumeCreate
{
	[self dismissModalViewControllerAnimated:YES];
	//[self startCreate];
	[self performSelector:@selector(startCreate) withObject:nil afterDelay:1];
}
-(void)kickHome
{
	[self.navigationController popViewControllerAnimated:YES];
}
-(void)checkValidation
{
	
	CheckValidationViewController *checkValidationController = [[CheckValidationViewController alloc] initWithNibName:@"CheckValidationViewController" bundle:nil];
	RootViewController *rootController = [self.navigationController.viewControllers objectAtIndex:0];
	[rootController presentModalViewController:checkValidationController animated:YES];
	[self.navigationController popViewControllerAnimated:YES];
	[checkValidationController release];
}
-(void)storeEventValues
{
	for (NSUInteger row = 0; row < 3; row++)
	{
		NSUInteger indexes[] = { 0, row };
		NSIndexPath *indexPath = [NSIndexPath indexPathWithIndexes:indexes
															length:2];
		
		EditableCell *cell = (EditableCell *)[[self tableView]
											  cellForRowAtIndexPath:indexPath];
		switch (row) {
			case EventNameCell:
				eventName = [[cell textField] text];
				break;
			case EventLocationCell:
				eventLocation = [[cell textField] text];
				break;
			case EventTimeCell:
				eventTime = [[cell textField] text];
				break;
			default:
				break;
		}
		
	}
	NSLog(@"eventName: %@", eventName);
	NSLog(@"eventLocation: %@", eventLocation);
	NSLog(@"eventTime: %@", eventTime);
}

- (EditableCell *)newEditableCellWithTag:(NSInteger)tag
{
    //EditableCell *cell = [[EditableCell alloc] initWithFrame:CGRectZero 
	//										 reuseIdentifier:nil];
    EditableCell *cell = [[EditableCell alloc] initWithFrame:CGRectZero];
    
    [[cell textField] setDelegate:self];
    [[cell textField] setTag:tag];
    
    return cell;
}


- (void)viewDidLoad {
	[[self navigationController] setNavigationBarHidden:NO animated:YES];
	NSString *myTitle = [[NSString alloc] initWithString:@"Event Creation"];
	UIBarButtonItem *cancelButton = [[UIBarButtonItem alloc]
									 initWithTitle:@"Cancel"
									 style:UIBarButtonItemStyleBordered
									 target:self
									 action:@selector(cancel:)];
	self.navigationItem.leftBarButtonItem = cancelButton;
	[cancelButton release];
	UIBarButtonItem *doneButton = [[UIBarButtonItem alloc]
								   initWithBarButtonSystemItem:UIBarButtonSystemItemSave
								   target:self action:@selector(startCreate)];
	self.navigationItem.rightBarButtonItem = doneButton;
	[doneButton release];
	self.title = myTitle;
	if (newGroup == nil) {
		UserGroup *newUserGroup = [[UserGroup alloc] init];
		newUserGroup.groupMembers = [[NSMutableArray alloc] initWithObjects: nil];
		newUserGroup.groupID = @"0";
		selectedGroupID	= @"0";
		self.newGroup = newUserGroup;
		[newUserGroup release];
	}
	eventName = [[NSString alloc] initWithString:@""];
	eventTime = [[NSString alloc] initWithString:@""];
	eventLocation = [[NSString alloc] initWithString:@""];
	[self setEventNameField: [self newEditableCellWithTag:EventNameCell]];
	[self setEventTimeField: [self newEditableCellWithTag:EventTimeCell]];
	[self setEventLocationField: [self newEditableCellWithTag:EventLocationCell]];
	[self setInviteOthers:NO];
	[self setBringGuests:NO];
	[self setStatusEmail:NO];
	[self setCancelEmail:NO];
	eventNeed = 0;
	eventDetails = @"";
	statusEmailDate = [[NSDate alloc] init];
	cancelEmailDate = [[NSDate alloc] init];
	//[now release];
	if (grouplist == nil) {
		NSMutableArray *groupArray = [[NSMutableArray alloc] init];
		self.grouplist = groupArray;
		[groupArray release];
	}
	/*int r, g, b;
	b = 205;
	g = 155;
	r = 39;
	self.tableView.backgroundColor = [UIColor colorWithRed:r/255.0f green:g/255.0f blue:b/255.0f alpha:1.0];*/
	self.tableView.backgroundColor = [UIColor clearColor];
    [super viewDidLoad];

    // Uncomment the following line to display an Edit button in the navigation bar for this view controller.
    // self.navigationItem.rightBarButtonItem = self.editButtonItem;
}



- (void)viewWillAppear:(BOOL)animated {
	NSLog(@"viewWillAppear");
	[self.tableView reloadData];
    [super viewWillAppear:animated];
}

- (void)viewDidAppear:(BOOL)animated {
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	if ([grouplist count] == 0 && ![settings.emailAddress isEqualToString:@"false"] && [settings.isValidated isEqualToString:@"true"]) {
		LoadingView *myLoadingView = [LoadingView loadingViewInView:[self.view.window.subviews objectAtIndex:0]];
		loadingView = myLoadingView;
		NSString * path = [[NSString alloc] initWithFormat:@"http://%@.dowehaveenough.com/devices/retrieve_groups.xml", [[[NSBundle mainBundle] infoDictionary] valueForKey:@"WEB_ENVIRONMENT"]];
		[self retrieveXMLFileAtURL:path];
		[path release];
	}
	[settings release];
	/*[self.navigationController setToolbarHidden:NO animated:YES];
	UIBarButtonItem *flexibleSpace = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemFlexibleSpace target:nil action:nil];
	UIBarButtonItem *inviteButton = [[UIBarButtonItem alloc]
									 initWithTitle:@"Create Event!" 
									 style:UIBarButtonItemStyleBordered 
									 target:self 
									 action:@selector(startCreate)];
	NSArray *items = [[NSArray alloc] initWithObjects:flexibleSpace, inviteButton, nil];
	self.toolbarItems = items;
	[flexibleSpace release];
	[inviteButton release];
	[items release];*/
    [super viewDidAppear:animated];
}
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


#pragma mark Table view methods

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
    return 3;
}
/*- (NSString *)tableView:(UITableView *)tableView
titleForHeaderInSection:(NSInteger)section
{
    switch (section)
    {
        case EventDetailsSection:  return @"The Event";
        case InvitePeopleSection: return @"The Peeps";
		case OptionsSection: return @"The Options";
    }
    
    return nil;
}*/
- (UIView *)tableView:(UITableView *)tableView viewForHeaderInSection:(NSInteger)section
{
	// create the parent view that will hold header Label
	UIView* customView = [[[UIView alloc] initWithFrame:CGRectMake(10,0,320,44)] autorelease];
	
	// create image object
	UIImage *myImage = nil;
	switch (section)
    {
        case EventDetailsSection:
		{
			myImage = [UIImage imageNamed:@"title_the_event.png"];
			/*UIImage *headerImage = [UIImage imageNamed:@"table_bg.png"];
			UIImageView *headerImageView = [[[UIImageView alloc] initWithImage:headerImage] autorelease];
			headerImageView.frame = CGRectMake(0,0,320,38);
			[customView addSubview:headerImageView];*/
			break;
        }
		case InvitePeopleSection: 
			myImage = [UIImage imageNamed:@"title_the_peeps.png"];
			break;
		case OptionsSection: 
			myImage = [UIImage imageNamed:@"title_the_options.png"];
			break;
    }
	
	
	
	// create the imageView with the image in it
	UIImageView *imageView = [[[UIImageView alloc] initWithImage:myImage] autorelease];
	imageView.frame = CGRectMake(20,10,240,24);
	
	[customView addSubview:imageView];
	
	if (section == EventDetailsSection) {
		//
	}

	
	return customView;
}
- (CGFloat)tableView:(UITableView *)tableView heightForHeaderInSection:(NSInteger)section
{
	return 44;
}

// Customize the number of rows in the table view.
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    switch (section)
    {
        case EventDetailsSection:  return 3;
        case InvitePeopleSection: return 2;
		case OptionsSection: return 2;
    }
    
    return 0;
}


// Customize the appearance of table view cells.
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    NSUInteger section = [indexPath section];
	NSUInteger row = [indexPath row];
	
	switch (section) {
		case EventDetailsSection:
		{
			EditableCell *cell = nil;
			NSInteger tag = INT_MIN;
			//NSString *text = nil;
			NSString *placeholder = nil;
			NSLog(@"row is %d", row);
			switch (row) {
				case EventNameCell:
				{
					//NSLog(@"setting cell title for event name: %@", eventName);
					cell = [self eventNameField];
					UIImage *cellImage = nil;
					cellImage = [UIImage imageNamed:@"icon_what.png"];
					cell.imageView.image = cellImage;
					//text = eventName;
					tag = EventNameCell;
					placeholder = @"What is the event?";
					break;
				}
				case EventLocationCell:
				{
					cell = [self eventLocationField];
					UIImage *cellImage = nil;
					cellImage = [UIImage imageNamed:@"icon_where.png"];
					cell.imageView.image = cellImage;
					//text = eventLocation;
					tag = EventLocationCell;
					placeholder = @"Where is it?";
					break;
				}
				case EventTimeCell:
				{
					cell = [self eventTimeField];
					UIImage *cellImage = nil;
					cellImage = [UIImage imageNamed:@"icon_when.png"];
					cell.imageView.image = cellImage;
					//text = eventTime;
					tag = EventTimeCell;
					placeholder = @"When is it?";
					break;
				}
				default:
				{
					break;
				}
			}
			UITextField *textField = [cell textField];
			[textField setTag:tag];
			//[textField setText:text];
			[textField setPlaceholder:placeholder];
			return cell;
			break;
			
		}
		case InvitePeopleSection:
		{
			switch (row) {
				case PeopleNeedCell:
				{
					static NSString *PeopleCellIdentifier = @"PeopleCellIdentifier";
					static NSString *PeopleCellDetailIdentifier = @"PeopleCellDetailIdentifier";
					UITableViewCell *cell = nil;
					if (eventNeed == 0) {
						cell = [tableView dequeueReusableCellWithIdentifier:PeopleCellIdentifier];
						if (cell == nil) {
							cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:PeopleCellIdentifier] autorelease];
						}
						cell.textLabel.text = @"You need . . .";
					} else {
						cell = [tableView dequeueReusableCellWithIdentifier:PeopleCellDetailIdentifier];
						if (cell == nil) {
							cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleSubtitle reuseIdentifier:PeopleCellDetailIdentifier] autorelease];
						}
						if (eventNeed == 1) {
							cell.textLabel.text = @"Need: 1 person";
							cell.detailTextLabel.text = @"* Remember to include yourself!";
						}else {
							NSString *needLabel = [[NSString alloc] initWithFormat:@"Need: %d people", eventNeed];
							cell.textLabel.text = needLabel;
							[needLabel release];
							cell.detailTextLabel.text = @"* Remember that this includes you!";
						}
					}
					UIImage *cellImage = nil;
					cellImage = [UIImage imageNamed:@"icon_need.png"];
					cell.imageView.image = cellImage;
					cell.accessoryType = UITableViewCellAccessoryDisclosureIndicator;
					return cell;
					break;
				}
				case PeopleInvitedCell:
				{
					static NSString *InviteOthersCellIdentifier = @"InviteOthersCellIdentifier";
					static NSString *InviteOthersDetailCellIdentifier = @"InviteOthersDetailCellIdentifier";
					
					NSUInteger numPeeps = [newGroup.groupMembers count];
					NSString *tempGroupName = [[NSString alloc] initWithString:@"New Group"];
					//NSString *tempGroupName;
					//NSString *btnTitle = nil;
					UITableViewCell *cell = nil;
					UserGroup *mygroup = nil;
					//NSLog(selectedGroupID);
					if (selectedGroupID != @"0") {
						for (mygroup in grouplist) {
							if (selectedGroupID == mygroup.groupID) {
								numPeeps = mygroup.groupMembersCount;
								tempGroupName = mygroup.groupName;
								break;
							}
						}
					} else {
						//mygroup = newGroup;
						//[tempGroupName release];
						if ([newGroup.groupName length] > 0) tempGroupName = newGroup.groupName;
						//else tempGroupName = @"New Group";
					}
					if (numPeeps > 0) {
						cell = [tableView dequeueReusableCellWithIdentifier:InviteOthersDetailCellIdentifier];
						if (cell == nil) {
							cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleSubtitle reuseIdentifier:InviteOthersDetailCellIdentifier] autorelease];
						}
						NSString *btnTitle = [[NSString alloc] initWithFormat:@"Invited: %@", tempGroupName];
						cell.textLabel.text = btnTitle;
						[btnTitle release];
						if (numPeeps == 1) {
							NSString *detailMsg = [[NSString alloc] initWithFormat:@"%d person (plus you)", numPeeps];
							cell.detailTextLabel.text = detailMsg;
							[detailMsg release];
						}else {
							NSString *detailMsg = [[NSString alloc] initWithFormat:@"%d people (plus you)", numPeeps];
							cell.detailTextLabel.text = detailMsg;
							[detailMsg release];
						}
						
						
						
					} else {
						cell = [tableView dequeueReusableCellWithIdentifier:InviteOthersCellIdentifier];
						if (cell == nil) {
							cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:InviteOthersCellIdentifier] autorelease];
						}
						NSString *btnTitle = [[NSString alloc] initWithFormat:@"You're Inviting . . .", numPeeps];
						cell.textLabel.text = btnTitle;
						[btnTitle release];
					}
					[tempGroupName release];
					UIImage *cellImage = nil;
					cellImage = [UIImage imageNamed:@"icon_people.png"];
					cell.imageView.image = cellImage;
					cell.accessoryType = UITableViewCellAccessoryDisclosureIndicator;
					cell.textLabel.adjustsFontSizeToFitWidth = YES;
					return cell;
					break;
				}
				default:
					break;
			}
			break;
			
		}
		case OptionsSection:
		{
			static NSString *OptionsCellIdentifier = @"OptionsCellIdentifier";
			static NSString *OptionsCellDetailIdentifier = @"OptionsCellDetailIdentifier";
			UITableViewCell *cell = nil;
			
			switch (row) {
				case EventDetailsCell:
					if ([eventDetails length] > 1) {
						cell = [tableView dequeueReusableCellWithIdentifier:OptionsCellDetailIdentifier];
						if (cell == nil) {
							cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleSubtitle reuseIdentifier:OptionsCellDetailIdentifier] autorelease];
						}
						cell.textLabel.text = @"Event Details";
						cell.detailTextLabel.text = eventDetails;
					} else {
						cell = [tableView dequeueReusableCellWithIdentifier:OptionsCellIdentifier];
						if (cell == nil) {
							cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:OptionsCellIdentifier] autorelease];
						}
						cell.textLabel.text = @"Add Event Details";
					}
					break;
				case EventOptionsCell:
					if (!inviteOthers && !bringGuests && !cancelEmail && !statusEmail) {
						cell = [tableView dequeueReusableCellWithIdentifier:OptionsCellIdentifier];
						if (cell == nil) {
							cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:OptionsCellIdentifier] autorelease];
						}
						cell.textLabel.text = @"Set Event Options";
					} else {
						int options = 0;
						if (inviteOthers) options++;
						if (bringGuests) options++;
						if (cancelEmail) options++;
						if (statusEmail) options++;
						cell = [tableView dequeueReusableCellWithIdentifier:OptionsCellDetailIdentifier];
						if (cell == nil) {
							cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleSubtitle reuseIdentifier:OptionsCellDetailIdentifier] autorelease];
						}
						cell.textLabel.text = @"Event Options";
						NSString *myDetails = [[NSString alloc] initWithFormat:@"%d/4 options set", options];
						cell.detailTextLabel.text = myDetails;
						[myDetails release];
					}
					
					break;
				default:
					break;
			}
			cell.accessoryType = UITableViewCellAccessoryDisclosureIndicator;
			return cell;
			break;
		}
		default:
		{
			static NSString *DeleteMeCellIdentifier = @"DeleteMeCellIdentifier";
			
			UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:DeleteMeCellIdentifier];
			if (cell == nil) {
				cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:DeleteMeCellIdentifier] autorelease];
			}
			return cell;
			break;
		}
	}
    return nil;
}


- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
    NSUInteger section = [indexPath section];
	NSUInteger row = [indexPath row];
	NSLog(@"its clicked");
	//[self storeEventValues];
	if (section == InvitePeopleSection) {
		if (row == PeopleNeedCell) {
			ChoosePeopleNeedViewController *choosePeopleNeedController = [[ChoosePeopleNeedViewController alloc] initWithNibName:@"ChoosePeopleNeedViewController" bundle:nil];
			choosePeopleNeedController.parentController = self;
			[self.navigationController pushViewController:choosePeopleNeedController animated:YES];
			[choosePeopleNeedController release];
		} else {
			if ([grouplist count] > 0) {
				ChooseGroupController *chooseGroupController = [[ChooseGroupController alloc] initWithStyle:UITableViewStyleGrouped];
				chooseGroupController.grouplist = self.grouplist;
				chooseGroupController.newGroup = self.newGroup;
				chooseGroupController.parentController = self;
				[self.navigationController pushViewController:chooseGroupController animated:YES];
				[chooseGroupController release];
			} else {
				ChooseUsersController *chooseUsersController = [[ChooseUsersController alloc] initWithStyle:UITableViewStyleGrouped];
				chooseUsersController.parentController2 = self;
				chooseUsersController.myParent = @"event";
				[self.navigationController pushViewController:chooseUsersController animated:YES];
				[chooseUsersController release];
			}
		}
		
	}
	if (section == OptionsSection) {
		if (row == EventDetailsCell) {
			AddNoteViewController *addNoteViewController = [[AddNoteViewController alloc] initWithNibName:@"AddNoteView" bundle:nil];
			addNoteViewController.title = @"Event Details";
			addNoteViewController.aNote = self.eventDetails;
			[self.navigationController pushViewController:addNoteViewController animated:YES];
			[addNoteViewController release];
		} else {
			EventOptionsViewController *evtOptionsController = [[EventOptionsViewController alloc] initWithStyle:UITableViewStyleGrouped];
			evtOptionsController.parentController = self;
			[self.navigationController pushViewController:evtOptionsController animated:YES];
			[evtOptionsController release];
		}
	}
	
	[tableView deselectRowAtIndexPath:indexPath animated:YES];
}


#pragma mark Action Sheet Methods
- (void)actionSheet:(UIActionSheet *)actionSheet didDismissWithButtonIndex:(NSInteger)buttonIndex
{
	if (buttonIndex == [actionSheet destructiveButtonIndex])
	{
		NSLog(@"destructive");
		//[self postCreateData];
		[self performSelector:@selector(postCreateData) withObject:nil afterDelay:.5];
	} else if (buttonIndex == [actionSheet cancelButtonIndex]) {
		NSLog(@"cancelled");
	}
	
}
#pragma mark UITextFieldDelegate Protocol

//  Sets the label of the keyboard's return key to 'Done' when the insertion
//  point moves to the table view's last field.
//
- (BOOL)textFieldShouldBeginEditing:(UITextField *)textField
{
    if ([textField tag] == EventLocationCell)
    {
        [textField setReturnKeyType:UIReturnKeyDone];
    }
    
    return YES;
}

//  UITextField sends this message to its delegate after resigning
//  firstResponder status. Use this as a hook to save the text field's
//  value to the corresponding property of the model object.
//
/*- (void)textFieldDidEndEditing:(UITextField *)textField
{
	NSLog(@"event name now: %@", eventName);
    NSString *text = [textField text];
    NSLog(@"text: %@", text);
	NSLog(@"tag: %d", [textField tag]);
    switch ([textField tag])
    {
        case EventNameCell:
		{
			//NSString *myEntry = [[NSString alloc] initWithFormat:@"%@", text];
			eventName = text;
			//[myEntry release];
			break;
		}
        case EventTimeCell:  
			eventTime = text;       
			break;
        case EventLocationCell: 
			eventLocation = text;      
			break;
    }
	NSLog(@"event name end: %@", eventName);
	//NSLog(@"event time: %@", eventTime);
}*/

//  UITextField sends this message to its delegate when the return key
//  is pressed. Use this as a hook to navigate back to the list view 
//  (by 'popping' the current view controller, or dismissing a modal nav
//  controller, as the case may be).
//
//  If the user is adding a new item rather than editing an existing one,
//  respond to the return key by moving the insertion point to the next cell's
//  textField, unless we're already at the last cell.
//
- (BOOL)textFieldShouldReturn:(UITextField *)textField
{
    if ([textField returnKeyType] != UIReturnKeyDone)
    {
        //  If this is not the last field (in which case the keyboard's
        //  return key label will currently be 'Next' rather than 'Done'), 
        //  just move the insertion point to the next field.
        //
        //  (See the implementation of -textFieldShouldBeginEditing: above.)
        //
        NSInteger nextTag = [textField tag] + 1;
        UIView *nextTextField = [[self tableView] viewWithTag:nextTag];
        
        [nextTextField becomeFirstResponder];
    } else {
		[textField resignFirstResponder];
	}
    
    return YES;
}
#pragma mark POST methods
- (void)retrieveXMLFileAtURL:(NSString *)URL {
	UIDevice *device = [UIDevice currentDevice];
	NSString *uniqueIdentifier = [device uniqueIdentifier];
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	NSString *postString = [NSString stringWithFormat:@"email_address=%@&device_id=%@", settings.emailAddress, uniqueIdentifier];
	//[uniqueIdentifier release];
    
    NSURL *url = [NSURL URLWithString:URL];
    NSMutableURLRequest *req = [NSMutableURLRequest requestWithURL:url];
    NSString *msgLength = [NSString stringWithFormat:@"%d", [postString length]];
	[settings release];
    
	[req addValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"Content-Type"];
	[req addValue:msgLength forHTTPHeaderField:@"Content-Length"];
    [req setHTTPMethod:@"POST"];
    [req setHTTPBody: [postString dataUsingEncoding:NSUTF8StringEncoding]];
    
    conn = [[NSURLConnection alloc] initWithRequest:req delegate:self];
    if (conn) {
        webData = [[NSMutableData data] retain];
    }
}
- (void)postCreateData {
	[self storeEventValues];
	LoadingView *myLoadingView = [LoadingView loadingViewInView:[self.view.window.subviews objectAtIndex:0]];
	loadingView = myLoadingView;
	UIDevice *device = [UIDevice currentDevice];
	NSString *uniqueIdentifier = [device uniqueIdentifier];
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	NSMutableString *postString = [NSMutableString stringWithFormat:@"email_address=%@&device_id=%@&event_name=%@&event_when=%@&event_where=%@&event_need=%d", 
							settings.emailAddress, 
							uniqueIdentifier,
							eventName,
							eventTime,
							eventLocation,
							eventNeed];
	//[uniqueIdentifier release];
	[settings release];
	if ([selectedGroupID isEqualToString:@"0"]) {
		
		[postString appendFormat:@"&group_id=0"];
		if ([newGroup.groupName length] >= 1) [postString appendFormat:@"&group_name=%@", newGroup.groupName];
		[postString appendString:@"&group_members="];
		for (int j=0; j<[newGroup.groupMembers count]; j++) {
			NSString *userEmail = [newGroup.groupMembers objectAtIndex:j];
			if (j != 0) [postString appendString:@","];
			[postString appendFormat:@"%@", userEmail];
			//[userEmail release];
		}
	} else {
		[postString appendFormat:@"&group_id=%@", selectedGroupID];
	}

	[postString appendFormat:@"&event_details=%@", eventDetails];
	if (inviteOthers) [postString appendString:@"&event_cannot_invite_others=1"];
	else [postString appendString:@"&event_cannot_invite_others=0"];
	if (bringGuests) [postString appendString:@"&event_cannot_bring_guests=1"];
	else [postString appendString:@"&event_cannot_bring_guests=0"];
	if (statusEmail) {
		NSDateFormatter *outputFormatter = [[NSDateFormatter alloc] init];
		[outputFormatter setDateFormat:@"Y-MM-dd HH:mm:00"];
		//NSDate *myDate = [[[NSDate alloc] init] autorelease];		// Get current date/time
		NSDate *myDate = [statusEmailDate addTimeInterval:[self calcTimezoneDiff]];
		NSString *newDateString = [outputFormatter stringFromDate:myDate];
		[postString appendFormat:@"&status_email=%@", newDateString];
		[outputFormatter release];
		//[myDate release];
	}
	if (cancelEmail) {
		NSDateFormatter *outputFormatter = [[NSDateFormatter alloc] init];
		[outputFormatter setDateFormat:@"Y-MM-dd HH:mm:00"];
		//NSDate *myCancelDate = [[[NSDate alloc] init] autorelease];		// Get current date/time
		NSDate *myCancelDate = [cancelEmailDate addTimeInterval:[self calcTimezoneDiff]];
		NSString *newDateString = [outputFormatter stringFromDate:myCancelDate];
		[postString appendFormat:@"&cancel_email=%@", newDateString];
		[outputFormatter release];
		//[myCancelDate release];
	}
	//NSLog(postString);
	NSString *URL = [[NSString alloc] initWithFormat:@"http://%@.dowehaveenough.com/devices/create_event.xml", [[[NSBundle mainBundle] infoDictionary] valueForKey:@"WEB_ENVIRONMENT"]];
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
    //NSLog(@"DONE. Received Bytes: %d", [webData length]);
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
	//NSLog(@"found file and started parsing");
}

- (void)parser:(NSXMLParser *)parser parseErrorOccurred:(NSError *)parseError {
	NSString * errorString = [NSString stringWithFormat:@"Unable to download group feed from web site (Error code %i )", [parseError code]];
	NSLog(@"error parsing XML: %@", errorString);
	
	UIAlertView * errorAlert = [[UIAlertView alloc] initWithTitle:@"Error loading content" message:errorString delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
	[errorAlert show];
	[errorAlert release];
}

- (void)parser:(NSXMLParser *)parser didStartElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName attributes:(NSDictionary *)attributeDict{
	//NSLog(@"found this element: %@", elementName);
	currentElement = [elementName copy];
	
	if ([elementName isEqualToString:@"group"]) {
		// clear out our story item caches...
		//item = [[NSMutableDictionary alloc] init];
		currentName = [[NSMutableString alloc] init];
		currentID = [[NSMutableString alloc] init];
		currentMembers = [[NSMutableString alloc] init];
	}
	if ([elementName isEqualToString:@"event_data"]) {
		submitStatus = [[NSMutableString alloc] init];
		submitEventID = [[NSMutableString alloc] init];
	}
}

- (void)parser:(NSXMLParser *)parser didEndElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qName{
	
	//NSLog(@"ended element: %@", elementName);
	if ([elementName isEqualToString:@"group"]) {
		// save values to an item, then store that item into the array...
		//[item setObject:currentTitle forKey:@"title"];
		//[item setObject:currentLink forKey:@"link"];
		//[item setObject:currentSummary forKey:@"summary"];
		//[item setObject:currentDate forKey:@"date"];
		UserGroup *group1 = [[UserGroup alloc] init];
		group1.groupName = [currentName stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]];;
		group1.groupID = [currentID stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]];;
		group1.groupMembersCount = [currentMembers intValue];
		[grouplist addObject:group1];
		[group1 release];
		
		//[eventlist addObject:[item copy]];
		//NSLog(@"adding group: %@", currentName);
	}
}

- (void)parser:(NSXMLParser *)parser foundCharacters:(NSString *)string{
	//NSLog(@"found characters: %@", string);
	// save the characters for the current item...
	if ([currentElement isEqualToString:@"id"]) {
		[currentID appendString:string];
	} else if ([currentElement isEqualToString:@"name"]) {
		[currentName appendString:string];
	} else if ([currentElement isEqualToString:@"member_count"]) {
		[currentMembers appendString:string];
	} else if ([currentElement isEqualToString:@"status"]) {
		[submitStatus appendString:string];
	} else if ([currentElement isEqualToString:@"event_id"]) {
		[submitEventID appendString:string];
	}
}

- (void)parserDidEndDocument:(NSXMLParser *)parser {
	
	//[activityIndicator stopAnimating];
	//[activityIndicator removeFromSuperview];
	
	NSLog(@"all done!");
	if ([submitStatus length] > 1) {
		NSString *myTitle = nil;
		NSString *myMsg = nil;
		NSString *mySubmitStatus = [submitStatus stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]];
		if ([mySubmitStatus isEqualToString:@"active"]) {
			myTitle = [[NSString alloc] initWithString:@"Whoo-hooo!"];
			myMsg = [[NSString alloc] initWithString:@"Your event was successfully created.\n\nDon't forget to set your own status!"];
		} else if ([mySubmitStatus isEqualToString:@"error"]) {
			myTitle = [[NSString alloc] initWithString:@"Whoops!"];
			myMsg = [[NSString alloc] initWithString:@"There was an error submitting your event.  Please try again."];
		} else if ([mySubmitStatus isEqualToString:@"pending_validation"]) {
			myTitle = [[NSString alloc] initWithString:@"Almost Done!"];
			myMsg = [[NSString alloc] initWithString:@"Your event is lined up and waiting.  We just need you to validate your email address to confirm your identity.\n\nPlease check your email now and follow the directions."];
		}
		UIAlertView *alert = [[UIAlertView alloc] initWithTitle:myTitle message:myMsg delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
		[alert show];
		[alert release];
		[myMsg release];
		[myTitle release];
	}
	//[self.tableView reloadData];
}

- (void)alertView:(UIAlertView *)alertView didDismissWithButtonIndex:(NSInteger)buttonIndex
{
	if ([submitEventID isEqualToString:@"false"]) {
		NSString *mySubmitStatus = [submitStatus stringByTrimmingCharactersInSet:[NSCharacterSet whitespaceAndNewlineCharacterSet]];
		if ([mySubmitStatus isEqualToString:@"error"]) {
			[self kickHome];
		} else {
			[self checkValidation];
		}

	} else {
		//NSArray *controllerArray = [[NSArray alloc] initWithObjects: gamePlayController, nil];
		//[self.navigationController setViewControllers:controllerArray animated:YES];
		RootViewController *rootController = [self.navigationController.viewControllers objectAtIndex:0];
		
		EventDetailsViewController *myController = [[EventDetailsViewController alloc] initWithStyle:UITableViewStyleGrouped];
		myController.event_id = submitEventID;
		//[self.navigationController pushViewController:myController animated:YES];
		NSArray *controllerArray = [[NSArray alloc] initWithObjects: rootController,myController, nil];
		[self.navigationController setViewControllers:controllerArray animated:NO];
		[myController release];
		[controllerArray release];
	}
}

- (void)dealloc {
	[_eventNameField release];
	[_eventLocationField release];
	[_eventTimeField release];
	[grouplist release];
	[newGroup release];
	[selectedGroupID release];
	[eventDetails release];
	[statusEmailDate release];
	[cancelEmailDate release];
	//[loadingView release];
	[eventName release];
	[eventTime release];
	[eventLocation release];
    [super dealloc];
}


@end

