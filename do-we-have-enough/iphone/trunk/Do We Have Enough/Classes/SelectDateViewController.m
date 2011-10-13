//
//  SelectDateViewController.m
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 10/25/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import "SelectDateViewController.h"
#import "CreateEventViewController.h"


@implementation SelectDateViewController
@synthesize theNotification;

-(IBAction)buttonPressed:(id)sender
{
	NSDate *selected = [datePicker date];
	CreateEventViewController *parentController = [self.navigationController.viewControllers objectAtIndex:1];
	if (theNotification == @"status")
		parentController.statusEmailDate = selected;
	else {
		parentController.cancelEmailDate = selected;
	}
	//[parentController release];
	//[selected release];
	[self.navigationController popViewControllerAnimated:YES];
}
-(IBAction)dateSelect:(id)sender
{
	NSDate *selected = [datePicker date];
	CreateEventViewController *parentController = [self.navigationController.viewControllers objectAtIndex:1];
	if (theNotification == @"status")
		parentController.statusEmailDate = selected;
	else {
		parentController.cancelEmailDate = selected;
	}
}

// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad {
	CreateEventViewController *parentController = [self.navigationController.viewControllers objectAtIndex:1];
	if (theNotification == @"status")
		[datePicker setDate:parentController.statusEmailDate];
	else {
		[datePicker setDate:parentController.cancelEmailDate];
	}
	//[parentController release];
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
	//self.datePicker = nil;
	self.theNotification = nil;
}


- (void)dealloc {
	[datePicker release];
	[theNotification release];
    [super dealloc];
}


@end
