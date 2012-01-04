//
//  CheckValidationViewController.h
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 11/18/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
@class LoadingView;

@interface CheckValidationViewController : UIViewController <NSXMLParserDelegate> {
	IBOutlet UIButton *checkButton;
	IBOutlet UIButton *restartButton;
	//xml parsing stuff
	NSXMLParser *xmlParser;
	NSString *currentElement;
	NSMutableString *currentResult;
	//---web service access---
    NSMutableData *webData;
    NSURLConnection *conn;
	LoadingView *loadingView;
    
    
}
@property (nonatomic, retain) UIButton *checkButton;
@property (nonatomic, retain) UIButton *restartButton;
@property (nonatomic, retain) LoadingView *loadingView;
- (void)retrieveXMLFileAtURL:(NSString *)URL;
-(IBAction)cancel:(id)sender;
-(IBAction)checkButtonPressed;


@end
