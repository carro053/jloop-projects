//
//  ChooseGroupController.m
//  Do We Have Enough
//
//  Created by Jay Dysart on 10/13/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import "ChooseGroupController.h"
#import "CreateEventViewController.h"
#import "ChooseUsersController.h"
#import "UserGroup.h"
#import "TestFlight.h"


@implementation ChooseGroupController
@synthesize grouplist, newGroup, parentController, lastIndexPath;
/*
- (id)initWithStyle:(UITableViewStyle)style {
    // Override initWithStyle: if you create the controller programmatically and want to perform customization that is not appropriate for viewDidLoad.
    if (self = [super initWithStyle:style]) {
    }
    return self;
}
*/


- (void)viewDidLoad {
    [super viewDidLoad];
    
    lastIndexPath = [[NSIndexPath alloc] initWithIndex:1];

    NSString *myTitle = [[NSString alloc] initWithString:@"Choose Group"];
	UIBarButtonItem *backButton = [[UIBarButtonItem alloc]
								   initWithTitle:myTitle
								   style:UIBarButtonItemStyleBordered
								   target:self
								   action:@selector(cancel:)];
	self.navigationItem.backBarButtonItem = backButton;
	self.title = myTitle;
	[backButton release];
	/*int r, g, b;
	 b = 205;
	 g = 155;
	 r = 39;
	 self.tableView.backgroundColor = [UIColor colorWithRed:r/255.0f green:g/255.0f blue:b/255.0f alpha:1.0];*/
	self.tableView.backgroundColor = [UIColor clearColor];
    [TestFlight passCheckpoint:@"CHOOSE GROUP VIEW"];
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
	//self.grouplist = nil;
	self.newGroup = nil;
	[super viewDidUnload];
}


#pragma mark Table view methods

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
	return 2;
}

/*- (NSString *)tableView:(UITableView *)tableView
titleForHeaderInSection:(NSInteger)section
{
	switch (section)
    {
        case NewGroupSection:  return @"Add New Group";
        case PastGroupSection: return @"My Past Groups";
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
        case NewGroupSection:
		{
			myImage = [UIImage imageNamed:@"title_add_new_group.png"];
			/*UIImage *headerImage = [UIImage imageNamed:@"table_bg.png"];
			 UIImageView *headerImageView = [[[UIImageView alloc] initWithImage:headerImage] autorelease];
			 headerImageView.frame = CGRectMake(0,0,320,38);
			 [customView addSubview:headerImageView];*/
			break;
        }
		case PastGroupSection: 
			myImage = [UIImage imageNamed:@"title_my_past_groups.png"];
			break;
    }
	// create the imageView with the image in it
	UIImageView *imageView = [[[UIImageView alloc] initWithImage:myImage] autorelease];
	if (section == 0) imageView.frame = CGRectMake(20,10,240,24);
	else imageView.frame = CGRectMake(20, 40, 240, 24);
	
	[customView addSubview:imageView];
	
	if (section == EventDetailsSection) {
		//
	}
	
	
	return customView;
}
- (CGFloat)tableView:(UITableView *)tableView heightForHeaderInSection:(NSInteger)section
{
	if (section == 0) return 44;
	else return 74;
}

// Customize the number of rows in the table view.
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    switch (section)
    {
        case NewGroupSection:  return 1;
        case PastGroupSection: return [self.parentController.grouplist count];
    }
	return 0;
}


// Customize the appearance of table view cells.
- (UITableViewCell *)tableView:(UITableView *)tableView 
		 cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    
    
    //[indexPath retain];
    
    
	NSUInteger section = [indexPath section];
	UITableViewCell *cell = nil;
	UIImage *cellImage = nil;
	//NSString *msg1 = [[NSString alloc] initWithFormat:@"parentController.selectedGroupID: %@", parentController.selectedGroupID];
	//NSLog(msg1);
	
	switch (section)
    {
        case NewGroupSection:
		{
			static NSString *NewCellIdentifier = @"NewGroupCell";
			
			UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:NewCellIdentifier];
			if (cell == nil) {
				cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleSubtitle reuseIdentifier:NewCellIdentifier] autorelease];
			}
			if (newGroup.groupName == nil || [newGroup.groupName length] < 1) 
				cell.textLabel.text = kNewGroupTitle;
			else cell.textLabel.text	 = newGroup.groupName;
			if ([newGroup.groupMembers count] != 0) cell.detailTextLabel.text = [[NSString alloc] initWithFormat:@"%d members", [newGroup.groupMembers count]];
			else cell.detailTextLabel.text = @"Click arrow to create a new list";
			cell.accessoryType = UITableViewCellAccessoryDetailDisclosureButton;
			if (parentController.selectedGroupID == @"0") {
				cellImage = [UIImage imageNamed:@"checkmark.png"];
				lastIndexPath = [indexPath copy];
			} else 
				cellImage = [UIImage imageNamed:@"checkmark-open.png"];
			cell.imageView.image = cellImage;
			//[NewCellIdentifier release];
			return cell;
		}
        case PastGroupSection: 
		{
			NSUInteger r = [indexPath row];
			UserGroup *group = [self.parentController.grouplist objectAtIndex:r];
			NSString *groupName = group.groupName;
			static NSString *CellIdentifier = @"Cell";
			
			UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
			if (cell == nil) {
				cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleSubtitle reuseIdentifier:CellIdentifier] autorelease];
			}
			cell.textLabel.text	 = groupName;
			NSString *rowSubString = [[NSString alloc] initWithFormat:@"%d members", group.groupMembersCount];
			cell.detailTextLabel.text = rowSubString;
			if (parentController.selectedGroupID == group.groupID) {
				lastIndexPath = [indexPath copy];
				cellImage = [UIImage imageNamed:@"checkmark.png"];
			} else 
				cellImage = [UIImage imageNamed:@"checkmark-open.png"];
			cell.imageView.image = cellImage;
			cell.textLabel.adjustsFontSizeToFitWidth = YES;
			//cell.image = 
			//cell.accessoryType = UITableViewCellAccessoryDetailDisclosureButton;
			return cell;
		}
    }
	return cell;
}

#pragma mark Table Delegate Methods


- (void)tableView:(UITableView *)tableView accessoryButtonTappedForRowWithIndexPath:(NSIndexPath *)indexPath {
	ChooseUsersController *chooseUsersController = [[ChooseUsersController alloc] initWithStyle:UITableViewStyleGrouped];
	chooseUsersController.parentController = self;
	chooseUsersController.myParent = @"group";
	[self.navigationController pushViewController:chooseUsersController animated:YES];
	//[self.navigationController pushViewController:chooseGroupController animated:YES];
	[chooseUsersController release];
}
- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {

    /*
    int oldRow;
    int oldSection;
    if(lastIndexPath != nil)
    {
        oldRow = [lastIndexPath row];
        oldSection = [lastIndexPath section];
    }
    else
    {
        oldRow = 0;
        oldSection = 0;
    }
    int newRow = [indexPath row];
    int newSection = [indexPath section];
     */
    
    
	int newRow = [indexPath row];
	int oldRow = [lastIndexPath row];
	int newSection = [indexPath section];
	int oldSection = [lastIndexPath section];
	
	if (newRow != oldRow || newSection != oldSection)
	{
		UITableViewCell *newCell = [tableView cellForRowAtIndexPath:indexPath];
		UIImage *cellImage = [UIImage imageNamed:@"checkmark.png"];
		newCell.imageView.image = cellImage;
		
		UITableViewCell *oldCell = [tableView cellForRowAtIndexPath: lastIndexPath]; 
		UIImage *cellImage2 = [UIImage imageNamed:@"checkmark-open.png"];
		oldCell.imageView.image = cellImage2;
		
		lastIndexPath = [indexPath copy];
		if (newSection != NewGroupSection) {
			UserGroup *group = [self.parentController.grouplist objectAtIndex:newRow];
			parentController.selectedGroupID = group.groupID;
			//NSString *msg2 = [[NSString alloc] initWithFormat:@"New group id: %@", group.groupID];
			//NSLog(msg2);
		} else {
			parentController.selectedGroupID = @"0";
		}
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


- (void)dealloc {
	[grouplist release];
	[newGroup release];
	[parentController release];
	[lastIndexPath release];
    [super dealloc];
}


@end

