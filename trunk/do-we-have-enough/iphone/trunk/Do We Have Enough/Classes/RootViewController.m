//
//  RootViewController.m
//  Do We Have Enough
//
//  Created by Jay Dysart on 10/13/09.
//  Copyright JLOOP 2009. All rights reserved.
//

#import "RootViewController.h"
#import "HomeController.h"
#import "SettingsController.h"
#import "SettingsTracker.h"


@implementation RootViewController
@synthesize homeController, settingsController;


- (void)viewDidLoad {
	[[self navigationController] setNavigationBarHidden:YES animated:NO];
	UIBarButtonItem *cancelButton = [[UIBarButtonItem alloc]
									 initWithTitle:@"Home"
									 style:UIBarButtonItemStyleBordered
									 target:self
									 action:@selector(cancel:)];
	self.navigationItem.backBarButtonItem = cancelButton;

    HomeController *homeViewController = [[HomeController alloc] initWithNibName:@"HomeController" bundle:nil];
	self.homeController = homeViewController;
	self.homeController.rootController = self;
	[self.view insertSubview:homeViewController.view atIndex:0];
	[homeViewController release];
	//SettingsTracker *settings = [[SettingsTracker alloc] init];
	//[settings resetData];
	//[settings release];
	self.navigationController.view.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"table_bg.png"]]; 
	
	
	[super viewDidLoad];
}
- (void)viewDidAppear:(BOOL)animated {
	[self.navigationController setToolbarHidden:YES animated:YES];
    [super viewDidAppear:animated];
}
- (IBAction)switchViews:(id)sender
{
	[UIView beginAnimations:@"View Flip" context:nil];
	[UIView setAnimationDuration:0.75];
	[UIView	setAnimationCurve:UIViewAnimationCurveEaseInOut];
	
	if (self.settingsController.view.superview == nil)
	{
		if (self.settingsController == nil)
		{
			SettingsController *settingsViewController = [[SettingsController alloc] initWithNibName:@"SettingsController" bundle:nil];
			self.settingsController = settingsViewController;
			self.settingsController.rootController = self;
			[settingsViewController release];
		}
		[UIView setAnimationTransition:UIViewAnimationTransitionCurlUp forView:self.view cache:NO];
		[homeController viewWillDisappear:YES];
		[settingsController viewWillAppear:YES];
		
		[homeController.view removeFromSuperview];
		[self.view insertSubview:settingsController.view atIndex:0];
		[settingsController viewDidAppear:YES];
		[homeController viewDidDisappear:YES];
	}
	else {
		if (self.homeController == nil) {
			HomeController *homeViewController = [[HomeController alloc] initWithNibName:@"HomeController" bundle:nil];
			self.homeController = homeViewController;
			[homeController release];
		}
		
		[UIView setAnimationTransition:UIViewAnimationTransitionCurlDown forView:self.view cache:YES];
		[settingsController viewWillDisappear:YES];
		[homeController viewWillAppear:YES];
		[settingsController.view removeFromSuperview];
		[self.view insertSubview:homeController.view atIndex:0];
		[homeController viewDidAppear:YES];
		[settingsController viewDidDisappear:YES];
	}
	[UIView commitAnimations];
}



- (void)viewWillAppear:(BOOL)animated {
	[[self navigationController] setNavigationBarHidden:YES animated:YES];
    [super viewWillAppear:animated];
}


- (void)didReceiveMemoryWarning {
	// Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
	
	// Release any cached data, images, etc that aren't in use.
}

- (void)viewDidUnload {
	// Release anything that can be recreated in viewDidLoad or on demand.
	// e.g. self.myOutlet = nil;
}



- (void)dealloc {
	[homeController release];
	[settingsController release];
    [super dealloc];
}


@end

