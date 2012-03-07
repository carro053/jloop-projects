//
//  ChooseUsersController.m
//  Do We Have Enough
//
//  Created by Jay Dysart on 10/15/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import "ChooseUsersController.h"
#import "ChooseGroupController.h"
#import "CreateEventViewController.h"
#import "UserGroup.h"
#import	"AddUserController.h"
#import "EditableCell.h"
#import "TestFlight.h"

@implementation ChooseUsersController
@synthesize parentController, textFieldBeingEdited, myParent, parentController2;
@synthesize groupNameField = _groupNameField;

#pragma mark -
//  Convenience method that returns a fully configured new instance of 
//  EditableDetailCell. Note that methods whose names begin with 'alloc' or
//  'new', or whose names contain 'copy', should return a non-autoreleased
//  instance with a retain count of one, as we do here.
//
- (EditableCell *)newEditableCellWithTag:(NSInteger)tag
{
    //EditableCell *cell = [[EditableCell alloc] initWithFrame:CGRectZero 
    //                                                     reuseIdentifier:nil];
    
    EditableCell *cell = [[EditableCell alloc] initWithFrame:CGRectZero];
    
    [[cell textField] setDelegate:self];
    [[cell textField] setTag:tag];
    
    return cell;
}

#pragma mark Address Book Methods
- (IBAction)showActionSheet:(id)sender {
	UIActionSheet *actionSheet = [[UIActionSheet alloc] initWithTitle:@"Select a method to add an email address." delegate:self cancelButtonTitle:@"Cancel" destructiveButtonTitle:nil otherButtonTitles:@"Select from Address Book", @"Enter Email Address", nil];
	[actionSheet showInView:self.view];
	[actionSheet release];
}
- (IBAction)showPicker:(id)sender {
    ABPeoplePickerNavigationController *picker = [[ABPeoplePickerNavigationController alloc] init];
	picker.displayedProperties = [NSArray arrayWithObject:[NSNumber numberWithInt:kABPersonEmailProperty]];
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
		//self.email.text = emailRef;
		[emailRef release];
		if (myParent == @"group") [self.parentController.newGroup.groupMembers addObject:emailRef];
		else [self.parentController2.newGroup.groupMembers addObject:emailRef];
		[self.tableView reloadData];
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
	AddUserController *addUserController = [[AddUserController alloc] init];
	if (myParent == @"group") addUserController.userlist = self.parentController.newGroup.groupMembers; 
	else addUserController.userlist = self.parentController2.newGroup.groupMembers;
	[self.navigationController pushViewController:addUserController animated:YES];
	//[self presentModalViewController:addUserController animated:YES];
	[addUserController release];
}




#pragma mark -
- (void)viewDidLoad {
	UIBarButtonItem *addEmailButton = [[UIBarButtonItem alloc]
									   initWithBarButtonSystemItem:UIBarButtonSystemItemAdd 
									   target:self
									   action:@selector(showActionSheet:)];
	
	self.navigationItem.rightBarButtonItem = addEmailButton;
	[addEmailButton release];
	[self setGroupNameField:[self newEditableCellWithTag:0]];
	[self.tableView setEditing:YES];
	
	NSString *myTitle = [[NSString alloc] initWithString:@"New Group"];
	UIBarButtonItem *backButton = [[UIBarButtonItem alloc]
								   initWithTitle:myTitle
								   style:UIBarButtonItemStyleBordered
								   target:self
								   action:@selector(cancel:)];
	self.navigationItem.backBarButtonItem = backButton;
	self.title = myTitle;
	[backButton release];
	[myTitle release];
	int numPeeps = 0;
	if (myParent == @"group") numPeeps = [self.parentController.newGroup.groupMembers count];
	else numPeeps = [self.parentController2.newGroup.groupMembers count];
	if (numPeeps == 0) [self showActionSheet:self];
	
	/*int r, g, b;
	 b = 205;
	 g = 155;
	 r = 39;
	 self.tableView.backgroundColor = [UIColor colorWithRed:r/255.0f green:g/255.0f blue:b/255.0f alpha:1.0];*/
	self.tableView.backgroundColor = [UIColor clearColor];
    [TestFlight passCheckpoint:@"CHOOSE USERS VIEW"];
	[super viewDidLoad];
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
- (void)viewWillAppear:(BOOL)animated {
	[self.tableView reloadData];
    [super viewWillAppear:animated];
}
-(void)viewWillDisappear:(BOOL)animated {
	if (textFieldBeingEdited != nil) {
		if (myParent == @"group") parentController.newGroup.groupName = self.textFieldBeingEdited.text;
		else parentController2.newGroup.groupName = self.textFieldBeingEdited.text;
	}
}
#pragma mark TextView Delegate Methods

- (void)textFieldDidBeginEditing:(UITextField *)textField
{
	self.textFieldBeingEdited = textField;
}
- (void)textFieldDidEndEditing:(UITextField *)textField
{
    NSLog(@"textFieldDidEnd");
    NSString *text = [textField text];
    if (myParent == @"group") parentController.newGroup.groupName = text;
	else parentController2.newGroup.groupName = text;
	//[textField resignFirstResponder];
}
- (BOOL)textFieldShouldReturn:(UITextField *)theTextField 
{
    
    [theTextField resignFirstResponder];
    // do stuff with the text
    NSString *text = [theTextField text];
	if (myParent == @"group") parentController.newGroup.groupName = text;
	else parentController2.newGroup.groupName = text;
    return YES;
}


#pragma mark Table view methods

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
    return 2;
}


// Customize the number of rows in the table view.
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
	switch (section) {
		case GroupNameSection:
			return 1;
			break;
		case GroupMemberSection:
			if (myParent == @"group") return [self.parentController.newGroup.groupMembers count];
			else return [self.parentController2.newGroup.groupMembers count];
			break;
		default:
			break;
	}
	return 0;
    
}
/*- (NSString *)tableView:(UITableView *)tableView
titleForHeaderInSection:(NSInteger)section
{
    switch (section)
    {
        case GroupNameSection:  return @"Group Name";
        case GroupMemberSection: return @"Group Members";
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
        case GroupNameSection:
		{
			myImage = [UIImage imageNamed:@"title_group_name.png"];
			/*UIImage *headerImage = [UIImage imageNamed:@"table_bg.png"];
			 UIImageView *headerImageView = [[[UIImageView alloc] initWithImage:headerImage] autorelease];
			 headerImageView.frame = CGRectMake(0,0,320,38);
			 [customView addSubview:headerImageView];*/
			break;
        }
		case GroupMemberSection: 
			myImage = [UIImage imageNamed:@"title_group_members.png"];
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

// Customize the appearance of table view cells.
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    NSUInteger section = [indexPath section];
	
	if (section == GroupNameSection) {
			EditableCell *cell = nil;
			NSInteger tag = INT_MIN;
			NSString *text = nil;
			NSString *placeholder = nil;
			
			cell = [self groupNameField];
			UIImage *cellImage = nil;
			cellImage = [UIImage imageNamed:@"icon_fpo.png"];
			cell.imageView.image = cellImage;
            if (myParent == @"group") text = self.parentController.newGroup.groupName;
			else text = self.parentController2.newGroup.groupName;
            tag = 0;
            placeholder = @"Name this group";
			UITextField *textField = [cell textField];
			[textField setTag:tag];
			[textField setText:text];
			[textField setPlaceholder:placeholder];
			return cell;
	} else {
			static NSString *DeleteMeCellIdentifier = @"DeleteMeCellIdentifier";
			
			UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:DeleteMeCellIdentifier];
			if (cell == nil) {
				cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:DeleteMeCellIdentifier] autorelease];
			}
			
			NSInteger row = [indexPath row];
			if (myParent == @"group") cell.textLabel.text = [self.parentController.newGroup.groupMembers objectAtIndex:row];
			else cell.textLabel.text = [self.parentController2.newGroup.groupMembers objectAtIndex:row];
		//default:
			return cell;
	}
    
	
    
}


- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
    // Navigation logic may go here. Create and push another view controller.
	// AnotherViewController *anotherViewController = [[AnotherViewController alloc] initWithNibName:@"AnotherView" bundle:nil];
	// [self.navigationController pushViewController:anotherViewController];
	// [anotherViewController release];
}



 // Override to support conditional editing of the table view.
 - (BOOL)tableView:(UITableView *)tableView canEditRowAtIndexPath:(NSIndexPath *)indexPath {
 // Return NO if you do not want the specified item to be editable.
	 NSUInteger section = [indexPath section];
	 switch (section) {
		 case GroupNameSection:
			 return NO;
			 break;
		 default:
			 return YES;
			 break;
	 }
	 return YES;
 }



// Override to support editing the table view.
- (void)tableView:(UITableView *)tableView commitEditingStyle:(UITableViewCellEditingStyle)editingStyle forRowAtIndexPath:(NSIndexPath *)indexPath {
    
    /*if (editingStyle == UITableViewCellEditingStyleDelete) {
	 // Delete the row from the data source
	 [tableView deleteRowsAtIndexPaths:[NSArray arrayWithObject:indexPath] withRowAnimation:YES];
	 }   
	 else if (editingStyle == UITableViewCellEditingStyleInsert) {
	 // Create a new instance of the appropriate class, insert it into the array, and add a new row to the table view
	 }  */
	NSUInteger row = [indexPath row];
	if (myParent == @"group") [self.parentController.newGroup.groupMembers removeObjectAtIndex:row];
	else [self.parentController2.newGroup.groupMembers removeObjectAtIndex:row];
	[tableView deleteRowsAtIndexPaths:[NSArray arrayWithObject:indexPath] withRowAnimation:UITableViewRowAnimationFade];
}



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
	[_groupNameField release];
	[parentController release];
	[textFieldBeingEdited release];
	[myParent release];
	[parentController2 release];
    [super dealloc];
}


@end

