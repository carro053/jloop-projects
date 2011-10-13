//
//  ValidateEmailCreateViewController.h
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 11/30/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
@class CreateEventViewController;


@interface ValidateEmailCreateViewController : UIViewController {
	IBOutlet UITextField *emailAddress;
	CreateEventViewController *parentController;
}
@property (nonatomic, retain) UITextField *emailAddress;
@property (nonatomic, retain) CreateEventViewController *parentController;
-(IBAction)cancel:(id)sender;
-(IBAction)save:(id)sender;


@end
