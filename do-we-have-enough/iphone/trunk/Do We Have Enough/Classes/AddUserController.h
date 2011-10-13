//
//  AddUserController.h
//  Do We Have Enough
//
//  Created by Jay Dysart on 10/13/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface AddUserController : UIViewController {
	UITextField *newEmail;
	NSMutableArray *userlist;
}
@property (nonatomic, retain) IBOutlet UITextField *newEmail;
@property (nonatomic, retain) NSMutableArray *userlist;
-(IBAction)textFieldDoneEditing:(id)sender;
-(IBAction)cancel:(id)sender;
-(IBAction)save:(id)sender;
@end
