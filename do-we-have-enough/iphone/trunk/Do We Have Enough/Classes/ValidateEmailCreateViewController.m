//
//  ValidateEmailCreateViewController.m
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 11/30/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import "ValidateEmailCreateViewController.h"
#import "CreateEventViewController.h"
#import "SettingsTracker.h"


@implementation ValidateEmailCreateViewController
@synthesize emailAddress;
@synthesize parentController;


-(IBAction)cancel:(id)sender {
	NSLog(@"cancel");
	//HomeController *homeController = self.parentViewController;
	
	[(CreateEventViewController *)self.parentViewController dismissModalViewControllerAnimated:YES];
}
-(IBAction)save:(id)sender {
	
	NSString *email = [emailAddress.text lowercaseString];
    NSString *emailRegEx =
    @"(?:[a-z0-9!#$%\\&'*+/=?\\^_`{|}~-]+(?:\\.[a-z0-9!#$%\\&'*+/=?\\^_`{|}"
    @"~-]+)*|\"(?:[\\x01-\\x08\\x0b\\x0c\\x0e-\\x1f\\x21\\x23-\\x5b\\x5d-\\"
    @"x7f]|\\\\[\\x01-\\x09\\x0b\\x0c\\x0e-\\x7f])*\")@(?:(?:[a-z0-9](?:[a-"
    @"z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\\[(?:(?:25[0-5"
    @"]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-"
    @"9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\\x01-\\x08\\x0b\\x0c\\x0e-\\x1f\\x21"
    @"-\\x5a\\x53-\\x7f]|\\\\[\\x01-\\x09\\x0b\\x0c\\x0e-\\x7f])+)\\])";
    
    NSPredicate *regExPredicate = [NSPredicate predicateWithFormat:@"SELF MATCHES %@", emailRegEx];
    BOOL myStringMatchesRegEx = [regExPredicate evaluateWithObject:email];
	if (myStringMatchesRegEx) {
		SettingsTracker *settings = [[SettingsTracker alloc] init];
		[settings initData];
		[settings saveEmail:emailAddress.text];
		[settings release];
		//[emailAddress resignFirstResponder];
		[parentController resumeCreate];
		
	} else {
		UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Incorrect Email" message:@"Please check the format of the email address you entered." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
		[alert show];
		[alert release];
	}
	
}

/*
 // The designated initializer.  Override if you create the controller programmatically and want to perform customization that is not appropriate for viewDidLoad.
- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil {
    if (self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil]) {
        // Custom initialization
    }
    return self;
}
*/

/*
// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad {
    [super viewDidLoad];
}
*/
-(void)viewDidAppear:(BOOL)animated
{
	[emailAddress becomeFirstResponder];
	[super viewDidAppear:animated];
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

- (void)viewDidDisappear:(BOOL)animated {
    [self.parentViewController performSelector:@selector(checkValidation) withObject:nil afterDelay:5.0];
}

- (void)dealloc {
	[emailAddress release];
	[parentController release];
    [super dealloc];
}


@end
