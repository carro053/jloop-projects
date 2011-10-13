//
//  ChoosePeopleNeedViewController.h
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 11/16/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
@class CreateEventViewController;


@interface ChoosePeopleNeedViewController : UIViewController <UIPickerViewDataSource, UIPickerViewDelegate>{
	CreateEventViewController *parentController;
	IBOutlet UIPickerView *needPicker;
}
@property (nonatomic, retain) CreateEventViewController *parentController;
@property (nonatomic, retain) UIPickerView *needPicker;

-(IBAction)cancel:(id)sender;
-(IBAction)save:(id)sender;
@end
