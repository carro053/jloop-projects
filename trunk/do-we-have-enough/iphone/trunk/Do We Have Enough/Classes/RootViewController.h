//
//  RootViewController.h
//  Do We Have Enough
//
//  Created by Jay Dysart on 10/13/09.
//  Copyright JLOOP 2009. All rights reserved.
//

#import <Foundation/Foundation.h>

@class HomeController;
@class SettingsController;

@interface RootViewController : UIViewController {
	HomeController *homeController;
	SettingsController *settingsController;
	
}

@property (nonatomic, retain) HomeController *homeController;
@property (nonatomic, retain) SettingsController *settingsController;


- (IBAction)switchViews:(id)sender;

@end
