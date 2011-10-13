//
//  ChoosePeopleNeedViewController.m
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 11/16/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import "ChoosePeopleNeedViewController.h"
#import "CreateEventViewController.h"


@implementation ChoosePeopleNeedViewController
@synthesize parentController;
@synthesize needPicker;

-(IBAction)cancel:(id)sender {
	[self.navigationController popViewControllerAnimated:YES];
}
-(IBAction)save:(id)sender {
	
	[parentController setEventNeed:[needPicker selectedRowInComponent:0]];
	[self.navigationController popViewControllerAnimated:YES];
	
	NSArray *allControllers = self.navigationController.viewControllers;
	UITableViewController *parent = [allControllers lastObject];
	
	[parent.tableView reloadData];
	//[newEmailText release];
	[parent release];
	
}

// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
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
	self.title = @"People Needed";
	
	[needPicker selectRow:parentController.eventNeed inComponent:0 animated:NO];
    [super viewDidLoad];
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
#pragma mark Picker Data Source Methods
- (NSInteger)numberOfComponentsInPickerView:(UIPickerView *)pickerView
{
	return 1;
}

- (NSInteger)pickerView:(UIPickerView *)pickerView numberOfRowsInComponent:(NSInteger)component
{
	return 100;
}
#pragma mark Picker Delegate Methods

- (NSString *)pickerView:(UIPickerView *)pickerView titleForRow:(NSInteger)row forComponent:(NSInteger)component
{
	NSString *myNum = nil;
	if (row == 0) myNum = [[NSString alloc] initWithFormat:@""];
	else if (row == 1) myNum = [[NSString alloc] initWithFormat:@"%d person", row];
	else myNum = [[NSString alloc] initWithFormat:@"%d people", row];
	[myNum autorelease];
	return myNum;
}



- (void)dealloc {
	[parentController release];
	[needPicker release];
    [super dealloc];
}


@end
