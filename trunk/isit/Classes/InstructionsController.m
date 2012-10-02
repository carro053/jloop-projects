//
//  InstructionsController.m
//  BlackMagic
//
//  Created by Michael Stratford on 12/5/10.
//  Copyright 2010 JLOOP. All rights reserved.
//

#import "InstructionsController.h"
#import "SettingsTracker.h";


@implementation InstructionsController
@synthesize pageOne;
@synthesize pageTwo;
@synthesize pageThree;
@synthesize pageFour;
@synthesize overlayOnSwitch;
@synthesize colorModePicker;
@synthesize arrayColors;
@synthesize topTitle;
@synthesize myTabBar;
@synthesize myTabBarItem;



// The designated initializer.  Override if you create the controller programmatically and want to perform customization that is not appropriate for viewDidLoad.
/*
- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization.
    }
    return self;
}
*/

// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad {
	arrayColors = [[NSMutableArray alloc] init];
	[arrayColors addObject:@"Dark"];
	[arrayColors addObject:@"Red"];
	[arrayColors addObject:@"Green"];
	[arrayColors addObject:@"Blue"];
	[arrayColors addObject:@"Bright"];
	[myTabBar setSelectedItem:myTabBarItem];
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	overlayOnSwitch.enabled = YES;
	[overlayOnSwitch setOn:[settings.overlayStartsOn intValue]];
	[colorModePicker selectRow:[settings.colorMode intValue] inComponent:0 animated:NO];
	[settings release];
    [super viewDidLoad];
	[[NSNotificationCenter defaultCenter] addObserver:self selector:@selector(appClosing) name:@"appClosing" object:nil];
}

- (NSInteger)numberOfComponentsInPickerView:(UIPickerView *)pickerView {
	return 1;
}

- (NSInteger)pickerView:(UIPickerView *)pickerView numberOfRowsInComponent:(NSInteger)component {
	
	return [arrayColors count];
}
- (NSString *)pickerView:(UIPickerView *)pickerView titleForRow:(NSInteger)row forComponent:(NSInteger)component {
	return [arrayColors objectAtIndex:row];
}

- (void)pickerView:(UIPickerView *)pickerView didSelectRow:(NSInteger)row inComponent:(NSInteger)component {
	NSLog(@"Comp");
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	NSString *color = [[NSString alloc] initWithFormat:@"%d", row];
	[settings saveColorMode:color];
	[color release];
	[settings release];
}

/*
// Override to allow orientations other than the default portrait orientation.
- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation {
    // Return YES for supported orientations.
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}
 */
- (void)tabBar:(UITabBar *)tabBar didSelectItem:(UITabBarItem *)item {
	NSLog(@"Touchdown");
	if(item.tag == 0)
	{
		topTitle.title = @"What is it?";
		[self.view bringSubviewToFront:pageOne];
	}
	if(item.tag == 1)
	{
		topTitle.title = @"How it works";
		[self.view bringSubviewToFront:pageTwo];
	}
	if(item.tag == 2)
	{
		topTitle.title = @"Instructions";
		[self.view bringSubviewToFront:pageThree];
	}
	if(item.tag == 3)
	{
		topTitle.title = @"Settings";
		[self.view bringSubviewToFront:pageFour];
	}
}
- (IBAction)backButtonPressed {
	[self dismissModalViewControllerAnimated:YES];
}

- (IBAction)toggleOverlayOn:(id)sender
{
	NSLog(@"%d", [sender isOn]);
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	NSString *overlayOn = [[NSString alloc] initWithFormat:@"%d", [sender isOn]];
	[settings saveOverlay:overlayOn];
	[overlayOn release];
	[settings release];
}


- (void)didReceiveMemoryWarning {
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc. that aren't in use.
}

- (void)viewDidUnload {
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}
- (void)appClosing
{
	NSLog(@"appClosing");
	[self dismissModalViewControllerAnimated:NO];
}


- (void)dealloc {
	[pageOne release];
	[pageTwo release];
	[pageThree release];
	[pageFour release];
	[overlayOnSwitch release];
	[colorModePicker release];
	[topTitle release];
	[arrayColors release];
	[myTabBar release];
	[myTabBarItem release];
	[[NSNotificationCenter defaultCenter] removeObserver:self name:@"appClosing" object:nil];
    [super dealloc];
}


@end
