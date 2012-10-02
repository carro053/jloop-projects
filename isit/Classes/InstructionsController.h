//
//  InstructionsController.h
//  BlackMagic
//
//  Created by Michael Stratford on 12/5/10.
//  Copyright 2010 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>


@interface InstructionsController : UIViewController <UIPickerViewDataSource, UIPickerViewDelegate, UITabBarDelegate> {
	
	IBOutlet UIView *pageOne;
	IBOutlet UIView *pageTwo;
	IBOutlet UIView *pageThree;
	IBOutlet UIView *pageFour;
	IBOutlet UISwitch *overlayOnSwitch;
	IBOutlet UIPickerView *colorModePicker;
	IBOutlet UINavigationItem *topTitle;
	IBOutlet UITabBar *myTabBar;
	IBOutlet UITabBarItem *myTabBarItem;
	NSMutableArray *arrayColors;
}
@property (nonatomic, retain) IBOutlet UIView *pageOne;
@property (nonatomic, retain) IBOutlet UIView *pageTwo;
@property (nonatomic, retain) IBOutlet UIView *pageThree;
@property (nonatomic, retain) IBOutlet UIView *pageFour;
@property (nonatomic, retain) IBOutlet UISwitch *overlayOnSwitch;
@property (nonatomic, retain) IBOutlet UIPickerView *colorModePicker;
@property (nonatomic, retain) IBOutlet UINavigationItem *topTitle;
@property (nonatomic, retain) IBOutlet UITabBar *myTabBar;
@property (nonatomic, retain) IBOutlet UITabBarItem *myTabBarItem;
@property (nonatomic, retain) NSMutableArray *arrayColors;

-(IBAction)backButtonPressed;
-(IBAction)toggleOverlayOn:(id)sender;

@end
