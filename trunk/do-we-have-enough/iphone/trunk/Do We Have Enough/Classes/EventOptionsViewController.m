//
//  EventOptionsViewController.m
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 10/24/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import "EventOptionsViewController.h"
#import "CreateEventViewController.h"
#import "SelectDateViewController.h"


@implementation EventOptionsViewController
@synthesize parentController;


-(IBAction)selectStatusDatePressed {
	NSLog(@"select status date pressed");
	SelectDateViewController *selectDateController = [[SelectDateViewController alloc] initWithNibName:@"SelectDateViewController" bundle:nil];
	selectDateController.theNotification = @"status";
	[self.navigationController pushViewController:selectDateController animated:YES];
	[selectDateController release];
}
-(IBAction)selectCancelDatePressed {
	NSLog(@"select cancel date pressed");
	SelectDateViewController *selectDateController = [[SelectDateViewController alloc] initWithNibName:@"SelectDateViewController" bundle:nil];
	selectDateController.theNotification = @"cancel";
	[self.navigationController pushViewController:selectDateController animated:YES];
	[selectDateController release];
}
/*
- (id)initWithStyle:(UITableViewStyle)style {
    // Override initWithStyle: if you create the controller programmatically and want to perform customization that is not appropriate for viewDidLoad.
    if (self = [super initWithStyle:style]) {
    }
    return self;
}
*/


- (void)viewDidLoad {
	NSString *myTitle = [[NSString alloc] initWithString:@"Event Options"];
	UIBarButtonItem *backButton = [[UIBarButtonItem alloc]
								   initWithTitle:myTitle
								   style:UIBarButtonItemStyleBordered
								   target:self
								   action:@selector(cancel:)];
	self.navigationItem.backBarButtonItem = backButton;
	self.title = myTitle;
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
	[self.tableView reloadData];
    [super viewWillAppear:animated];
}

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
    return 2;
}


// Customize the number of rows in the table view.
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
	return 2;
}
/*- (NSString *)tableView:(UITableView *)tableView
titleForHeaderInSection:(NSInteger)section
{
    switch (section)
    {
        case UserOptionsSection:  return @"User Options";
        case NotificationOptionsSection: return @"Notification Options";
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
        case UserOptionsSection:
		{
			myImage = [UIImage imageNamed:@"title_user_options.png"];
			break;
        }
		case NotificationOptionsSection: 
			myImage = [UIImage imageNamed:@"title_notification_options.png"];
			break;
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

// Customize the appearance of table view cells.
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    NSUInteger section = [indexPath section];
	NSUInteger row = [indexPath row];
	UITableViewCell *cell = nil;
	UIImage *cellImage = nil;
		
	switch (section)
    {
        case UserOptionsSection:
		{
			
			static NSString *NewCellIdentifier = @"UserOptionsCell";
			
			UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:NewCellIdentifier];
			if (cell == nil) {
				cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:NewCellIdentifier] autorelease];
			}
			switch (row) {
				case InviteOthersCell:
					cell.textLabel.text = @"Guests cannot invite others";
					if (parentController.inviteOthers) {
						cellImage = [UIImage imageNamed:@"checkbox.png"];
						//lastIndexPath = indexPath;
					} else 
						cellImage = [UIImage imageNamed:@"checkbox-open.png"];
					cell.imageView.image = cellImage;
					break;
				case BringGuestsCell:
					cell.textLabel.text = @"Guests cannot bring guests";
					if (parentController.bringGuests) {
						cellImage = [UIImage imageNamed:@"checkbox.png"];
						//lastIndexPath = indexPath;
					} else 
						cellImage = [UIImage imageNamed:@"checkbox-open.png"];
					cell.imageView.image = cellImage;
					break;
				default:
					break;
			}
			
			return cell;
		}
        case NotificationOptionsSection: 
		{
			static NSString *NotificationCellIdentifier = @"NotificationOptionsCell";
			UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:NotificationCellIdentifier];
			if (cell == nil) {
				cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleSubtitle reuseIdentifier:NotificationCellIdentifier] autorelease];
			}
			NSDateFormatter *outputFormatter = [[NSDateFormatter alloc] init];
			[outputFormatter setDateFormat:@"'on' EEE',' MMM d 'at' h:mma"];
			//[outputFormatter setTimeStyle:NSDateFormatterShortStyle];
			//[outputFormatter setDateStyle:NSDateFormatterMediumStyle];
			NSDate *now = [[NSDate alloc] init];
			NSDate *compareCancel = [now earlierDate:parentController.cancelEmailDate];
			NSDate *compareStatus = [now earlierDate:parentController.statusEmailDate];
			switch (row) {
				case StatusEmailCell:
					if (now == compareStatus) { //status email is in the future (ie: good)
						NSString *newDateString = [outputFormatter stringFromDate:parentController.statusEmailDate];
						cell.detailTextLabel.text = newDateString;
						cell.accessoryType = UITableViewCellAccessoryDetailDisclosureButton;
						if (parentController.statusEmail)
							cellImage = [UIImage imageNamed:@"checkbox.png"];
						else 
							cellImage = [UIImage imageNamed:@"checkbox-open.png"];
					} else {
						cell.detailTextLabel.text = kSelectTime;
						[parentController setStatusEmail:NO];
						cellImage = [UIImage imageNamed:@"checkbox-open.png"];
					}
					
					cell.imageView.image = cellImage;
					cell.textLabel.text = @"Send Status Update";
					break;
				case CancelEmailCell:
					
					if (now == compareCancel) { //cancel is in the future (ie: good)
						
						NSString *newDateString2 = [outputFormatter stringFromDate:parentController.cancelEmailDate];
						cell.detailTextLabel.text = newDateString2;
						cell.accessoryType = UITableViewCellAccessoryDetailDisclosureButton;
						if (parentController.cancelEmail) {
							cellImage = [UIImage imageNamed:@"checkbox.png"];
						} else 
							cellImage = [UIImage imageNamed:@"checkbox-open.png"];
					} else { //
						cell.detailTextLabel.text = kSelectTime;
						[parentController setCancelEmail:NO];
						cellImage = [UIImage imageNamed:@"checkbox-open.png"];
					}						
					
					cell.imageView.image = cellImage;
					cell.textLabel.text = @"Send Cancellation";
					break;
				default:
					break;
			}
			
			[outputFormatter release];
			[now release];
			return cell;
		}
    }
	return cell;
}


#pragma mark Table Delegate Methods

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
	
	NSUInteger section = [indexPath section];
	NSUInteger row = [indexPath row];
	
	switch (section) {
		case UserOptionsSection:
			switch (row) {
				case InviteOthersCell:
					if (self.parentController.inviteOthers) {
						UITableViewCell *newCell = [tableView cellForRowAtIndexPath:indexPath];
						UIImage *cellImage = [UIImage imageNamed:@"checkbox-open.png"];
						newCell.imageView.image = cellImage;
						[self.parentController setInviteOthers:NO];
					} else {
						UITableViewCell *newCell = [tableView cellForRowAtIndexPath:indexPath];
						UIImage *cellImage = [UIImage imageNamed:@"checkbox.png"];
						newCell.imageView.image = cellImage;
						[self.parentController setInviteOthers:YES];
					}
					break;
				case BringGuestsCell:
					if (self.parentController.bringGuests) {
						UITableViewCell *newCell = [tableView cellForRowAtIndexPath:indexPath];
						UIImage *cellImage = [UIImage imageNamed:@"checkbox-open.png"];
						newCell.imageView.image = cellImage;
						[self.parentController setBringGuests:NO];
					} else {
						UITableViewCell *newCell = [tableView cellForRowAtIndexPath:indexPath];
						UIImage *cellImage = [UIImage imageNamed:@"checkbox.png"];
						newCell.imageView.image = cellImage;
						[self.parentController setBringGuests:YES];
					}
					break;
				default:
					break;
			}
			break;
		case NotificationOptionsSection:
			switch (row) {
				case StatusEmailCell:
					if (self.parentController.statusEmail) {
						UITableViewCell *newCell = [tableView cellForRowAtIndexPath:indexPath];
						UIImage *cellImage = [UIImage imageNamed:@"checkbox-open.png"];
						newCell.imageView.image = cellImage;
						[self.parentController setStatusEmail:NO];
					} else {
						UITableViewCell *newCell = [tableView cellForRowAtIndexPath:indexPath];
						UIImage *cellImage = [UIImage imageNamed:@"checkbox.png"];
						newCell.imageView.image = cellImage;
						if (newCell.detailTextLabel.text == kSelectTime) [self selectStatusDatePressed];
						[self.parentController setStatusEmail:YES];
					}
					break;
				case CancelEmailCell:
					if (self.parentController.cancelEmail) {
						UITableViewCell *newCell = [tableView cellForRowAtIndexPath:indexPath];
						UIImage *cellImage = [UIImage imageNamed:@"checkbox-open.png"];
						newCell.imageView.image = cellImage;
						[self.parentController setCancelEmail:NO];
					} else {
						UITableViewCell *newCell = [tableView cellForRowAtIndexPath:indexPath];
						UIImage *cellImage = [UIImage imageNamed:@"checkbox.png"];
						newCell.imageView.image = cellImage;
						if (newCell.detailTextLabel.text == kSelectTime) [self selectCancelDatePressed];
						[self.parentController setCancelEmail:YES];
					}
					break;
				default:
					break;
			}
			break;

		default:
			break;
	}
	
	[tableView deselectRowAtIndexPath:indexPath animated:YES];
}

- (void)tableView:(UITableView *)tableView accessoryButtonTappedForRowWithIndexPath:(NSIndexPath *)indexPath {
	NSUInteger section = [indexPath section];
	NSUInteger row = [indexPath row];
	if (section == NotificationOptionsSection) {
		if (row == StatusEmailCell) [self selectStatusDatePressed];
		else [self selectCancelDatePressed];
	}
}


- (void)dealloc {
	[parentController release];
    [super dealloc];
}


@end

