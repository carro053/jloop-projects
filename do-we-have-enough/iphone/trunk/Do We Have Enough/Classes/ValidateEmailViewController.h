//
//  ValidateEmailViewController.h
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 11/18/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import <UIKit/UIKit.h>
@class LoadingView;


@interface ValidateEmailViewController : UIViewController <NSXMLParserDelegate> {
	IBOutlet UITextField *emailAddress;
	//xml parsing stuff
	NSXMLParser *xmlParser;
	NSString *currentElement;
	NSMutableString *currentResult;
	//---web service access---
    NSMutableData *webData;
    NSURLConnection *conn;
	LoadingView *loadingView;
}
@property (nonatomic, retain) UITextField *emailAddress;
@property (nonatomic, retain) LoadingView *loadingView;
- (void)retrieveXMLFileAtURL:(NSString *)URL;
-(IBAction)cancel:(id)sender;
-(IBAction)save:(id)sender;
@end
