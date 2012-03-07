//
//  AddUserController.m
//  Do We Have Enough
//
//  Created by Jay Dysart on 10/13/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import "AddUserController.h"
#import "TestFlight.h"


@implementation AddUserController
@synthesize newEmail, userlist;


-(IBAction)textFieldDoneEditing:(id)sender {
	//NSLog(@"when");
	[self save:sender];
	//[sender resignFirstResponder];
}
-(IBAction)cancel:(id)sender {
	[self.navigationController popViewControllerAnimated:YES];
}
-(IBAction)save:(id)sender {
	
	NSString *email = [newEmail.text lowercaseString];
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
		NSString *newEmailText = [[NSString alloc] initWithString:newEmail.text];
		[userlist addObject:[newEmailText lowercaseString]];
		[self.navigationController popViewControllerAnimated:YES];
	
		NSArray *allControllers = self.navigationController.viewControllers;
		UITableViewController *parent = [allControllers lastObject];

		[parent.tableView reloadData];
		[newEmailText release];
		//[parent release];
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


// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad {
	[newEmail becomeFirstResponder];
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
	self.title = @"Invite someone";
	self.view.backgroundColor = [UIColor clearColor];
    [TestFlight passCheckpoint:@"ADD USER VIEW"];
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


- (void)dealloc {
	[newEmail release]; 
	[userlist release];
    [super dealloc];
}


@end
