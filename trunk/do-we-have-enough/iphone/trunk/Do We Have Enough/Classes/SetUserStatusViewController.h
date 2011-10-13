//
//  SetUserStatusViewController.h
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 11/20/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
@class EventDetailsViewController;
@class LoadingView;


@interface SetUserStatusViewController : UIViewController <UIPickerViewDataSource, UIPickerViewDelegate, NSXMLParserDelegate> {
	IBOutlet UIPickerView *statusPicker;
	int myStatus;
	int myGuests;
	int cannotBringGuests;
	EventDetailsViewController *parentController;
	NSString *event_id;
	//---xml parser stuff
	NSXMLParser *xmlParser;
	NSString *currentElement;
	NSMutableString *currentResult;
	//---web service access---
    NSMutableData *webData;
    NSURLConnection *conn;
	LoadingView *loadingView;
}
@property (nonatomic, retain) UIPickerView *statusPicker;
@property (nonatomic, readwrite) int myStatus;
@property (nonatomic, readwrite) int myGuests;
@property (nonatomic, readwrite) int cannotBringGuests;
@property (nonatomic, retain) EventDetailsViewController *parentController;
@property (nonatomic, retain) LoadingView *loadingView;
@property (nonatomic, retain) NSString *event_id;
-(IBAction)cancel:(id)sender;
-(IBAction)save:(id)sender;
- (void)retrieveXMLFileAtURL:(NSString *)URL;
@end
