//
//  EventMemberListViewController.m
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 11/23/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import "EventMemberListViewController.h"



@implementation EventMemberListViewController
@synthesize memberlist, inlist, outlist, fiftylist, unknownlist;

/*
- (id)initWithStyle:(UITableViewStyle)style {
    // Override initWithStyle: if you create the controller programmatically and want to perform customization that is not appropriate for viewDidLoad.
    if (self = [super initWithStyle:style]) {
    }
    return self;
}
*/


- (void)viewDidLoad {
	inlist = [[NSMutableArray alloc] init];
	outlist	= [[NSMutableArray alloc] init];
	fiftylist = [[NSMutableArray alloc] init];
	unknownlist = [[NSMutableArray alloc] init];
	
	for (int i=0; i<[memberlist count]; i++) {
		NSMutableDictionary *myMember = [memberlist objectAtIndex:i];
		NSMutableString *myStatus = [[NSMutableString alloc] initWithString:[myMember objectForKey:@"status"]];
		//NSMutableString *myName = [[NSString alloc] initWithString:[myMember objectForKey:@"name"]];
		//NSLog(@"%@", myName);
		int myStatusInt = [myStatus intValue];
		//int myGuestsInt = [[myMember objectForKey:@"guests"] intValue];
		if (myStatusInt == 1) [inlist addObject:[myMember copy]];
		else if (myStatusInt == 2) [outlist addObject:[myMember copy]];
		else if (myStatusInt == 3) [fiftylist addObject:[myMember copy]];
		else [unknownlist addObject:[myMember copy]];
		[myStatus release];
		//[myName release];
	}
	[super viewDidLoad];
}
- (void)viewDidAppear:(BOOL)animated {
	
	
    [super viewDidAppear:animated];

    // Uncomment the following line to display an Edit button in the navigation bar for this view controller.
    // self.navigationItem.rightBarButtonItem = self.editButtonItem;
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
    return 4;
}

- (NSString *)tableView:(UITableView *)tableView
titleForHeaderInSection:(NSInteger)section
{
    switch (section)
    {
		case InUsersSection: return @"IN";
        case FiftyUsersSection:  return @"50/50";
        case OutUsersSection: return @"OUT";
		case UnknownUsersSection: return @"NO REPLY";
    }
    
    return nil;
}


// Customize the number of rows in the table view.
- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section {
    switch (section)
    {
		case InUsersSection: return [inlist count];
        case FiftyUsersSection:  return [fiftylist count];
        case OutUsersSection: return [outlist count];
		case UnknownUsersSection: return [unknownlist count];
    }
    
    return 0;
}


// Customize the appearance of table view cells.
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath {
    NSUInteger section = [indexPath section];
	NSUInteger row = [indexPath row];
	
    static NSString *CellIdentifier = @"Cell";
    
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier];
    if (cell == nil) {
        cell = [[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CellIdentifier] autorelease];
    }
    
    switch (section)
    {
		case InUsersSection:
		{
			NSMutableDictionary *myMember = [inlist objectAtIndex:row];
			int myGuests = [[myMember objectForKey:@"guests"] intValue];
			NSString *myMsg = nil;
			if (myGuests == 0) {
				myMsg = [[NSString alloc] initWithFormat:@"%@", [myMember objectForKey:@"name"]];
			} else {
				myMsg = [[NSString alloc] initWithFormat:@"%@+ %d", [myMember objectForKey:@"name"], myGuests];
			}
			cell.textLabel.text = myMsg;
			[myMsg release];
			break;
		}
        case FiftyUsersSection:
		{
			NSMutableDictionary *myMember = [fiftylist objectAtIndex:row];
			cell.textLabel.text = [myMember objectForKey:@"name"];
			break;
		}
        case OutUsersSection:
		{
			NSMutableDictionary *myMember = [outlist objectAtIndex:row];
			cell.textLabel.text = [myMember objectForKey:@"name"];
			break;
		}
		case UnknownUsersSection:
		{
			NSMutableDictionary *myMember = [unknownlist objectAtIndex:row];
			cell.textLabel.text = [myMember objectForKey:@"name"];
			break;
		}
    }
    
	
    return cell;
}


- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath {
    // Navigation logic may go here. Create and push another view controller.
	// AnotherViewController *anotherViewController = [[AnotherViewController alloc] initWithNibName:@"AnotherView" bundle:nil];
	// [self.navigationController pushViewController:anotherViewController];
	// [anotherViewController release];
	[tableView deselectRowAtIndexPath:indexPath animated:NO];
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
	[inlist release];
	[outlist release];
	[fiftylist release];
	[unknownlist release];
    [super dealloc];
}


@end

