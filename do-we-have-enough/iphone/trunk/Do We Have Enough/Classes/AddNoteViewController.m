//
//  AddNoteViewController.m
//  UITextView
//
//  Created by Ellen Miner on 3/7/09.
//  Copyright 2009 RaddOnline. All rights reserved.
//

#import "AddNoteViewController.h"
#import "CreateEventViewController.h"

//Text View contstants
#define kUITextViewCellRowHeight 150.0

@implementation AddNoteViewController
@synthesize aNote;

- (void)save:(id)sender
{
	/*
	 Save data from the text view to the variable and then pop back to the root view.
	 Normally , this is where I would save data to the database. To keep the example simple
	 I am simply setting the variable in the root controller to match that of my note.
	*/
	
	TextViewCell *cell = (TextViewCell *) [tbView cellForRowAtIndexPath:[NSIndexPath indexPathForRow:0 inSection:0]];
	self.aNote = [cell.textView text];
	//NSLog(self.aNote);
	CreateEventViewController *parentController = [self.navigationController.viewControllers objectAtIndex:1];
	parentController.eventDetails = self.aNote;
	[self.navigationController popViewControllerAnimated:YES];
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
    
	/*UIBarButtonItem *cancelButton = [[UIBarButtonItem alloc]
									 initWithTitle:@"Cancel"
									 style:UIBarButtonItemStyleBordered
									 target:self
									 action:@selector(cancel:)];
	self.navigationItem.leftBarButtonItem = cancelButton;
	[cancelButton release];

	// provide a Save button to dismiss the keyboard
	UIBarButtonItem* saveItem = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemSave
																			  target:self action:@selector(save:)];
	self.navigationItem.rightBarButtonItem = saveItem;
	[saveItem release];*/
	int r, g, b;
	b = 205;
	g = 155;
	r = 39;
	self.tableView.backgroundColor = [UIColor colorWithRed:r/255.0f green:g/255.0f blue:b/255.0f alpha:1.0];
	[super viewDidLoad];
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

- (void)viewWillDisappear:(BOOL)animated {
	TextViewCell *cell = (TextViewCell *) [tbView cellForRowAtIndexPath:[NSIndexPath indexPathForRow:0 inSection:0]];
	self.aNote = [cell.textView text];
	//NSLog(self.aNote);
	CreateEventViewController *parentController = [self.navigationController.viewControllers objectAtIndex:1];
	parentController.eventDetails = self.aNote;
	[super viewWillDisappear:animated];
}

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
    [super didReceiveMemoryWarning]; // Releases the view if it doesn't have a superview
    // Release anything that's not essential, such as cached data
}

#pragma mark Table view methods

- (UITableViewCellEditingStyle)tableView:(UITableView *)tableView editingStyleForRowAtIndexPath:(NSIndexPath *)indexPath {
	return UITableViewCellEditingStyleNone;
}

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
    return 1;
}


// Customize the number of rows in the table view.
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    return 1;
}


- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
	CGFloat result;
	result = kUITextViewCellRowHeight;	
	return result;
}

// Customize the appearance of table view cells.
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    
    TextViewCell *cell = (TextViewCell *) [tableView dequeueReusableCellWithIdentifier:kCellTextView_ID];
	
    if (cell == nil) {
		cell = [TextViewCell createNewTextCellFromNib];
    }
    
    // Set up the cell...
	cell.textView.text = self.aNote;
	[cell.textView becomeFirstResponder];
	cell.textView.delegate = self;
    return cell;
}


- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
    // Navigation logic may go here. Create and push another view controller.
	// AnotherViewController *anotherViewController = [[AnotherViewController alloc] initWithNibName:@"AnotherView" bundle:nil];
	// [self.navigationController pushViewController:anotherViewController];
	// [anotherViewController release];
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

#pragma mark UITextView delegate methods

- (void)textViewDidBeginEditing:(UITextView *)textView
{
	
}
- (void)textViewDidEndEditing:(UITextView *)textView
{
	//NSString *msg = [[NSString alloc] initWithFormat:@"text: %@", textView.text];
	//NSLog(msg);
	CreateEventViewController *parentController = [self.navigationController.viewControllers objectAtIndex:1];
	parentController.eventDetails = textView.text;
}


- (void)dealloc {
	[aNote release];
    [super dealloc];
}


@end

