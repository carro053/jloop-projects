//
//  SelectDateViewController.h
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 10/25/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface SelectDateViewController : UIViewController <UIPickerViewDelegate> {
	IBOutlet UIDatePicker *datePicker;
	NSString *theNotification;
}
@property (nonatomic, retain) NSString *theNotification;
-(IBAction)buttonPressed:(id)sender;
-(IBAction)dateSelect:(id)sender;
@end
