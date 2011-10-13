//
//  SettingsTracker.h
//  DoWeHaveEnough
//
//  Created by Jay Dysart on 11/18/09.
//  Copyright 2009 JLOOP. All rights reserved.
//

#import <Foundation/Foundation.h>

#define kFilename	@"dwhedata.plist"


@interface SettingsTracker : NSObject {
	NSString *emailAddress;
	NSString *isValidated;
	NSString *notifyIn;
	NSString *notifyOut;
	NSString *notifyPush;
	BOOL isInited;
}
@property (nonatomic, retain) NSString *emailAddress;
@property (nonatomic, retain) NSString *isValidated;
@property (nonatomic, retain) NSString *notifyIn;
@property (nonatomic, retain) NSString *notifyOut;
@property (nonatomic, retain) NSString *notifyPush;
@property (nonatomic, readwrite) BOOL isInited;
-(NSString *)dataFilePath;
-(void)saveEmail:(NSString *)email;
-(void)saveValidation:(NSString *)validation;
-(void)saveNotifyIn:(NSString *)notify;
-(void)saveNotifyOut:(NSString *)notify;
-(void)saveNotifyPush:(NSString *)notify;
-(void)initData;
-(void)resetData;

@end
