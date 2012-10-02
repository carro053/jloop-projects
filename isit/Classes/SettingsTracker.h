//
//  SettingsTracker.h
//  IsIt
//
//  Created by Michael Stratford on 12/07/10.
//

#import <UIKit/UIKIt.h>

#define kFilename	@"isitdata.plist"


@interface SettingsTracker : NSObject {
	NSString *overlayStartsOn;
	NSString *colorMode;
	BOOL isInited;
}
@property (nonatomic, retain) NSString *overlayStartsOn;
@property (nonatomic, retain) NSString *colorMode;
@property (nonatomic, readwrite) BOOL isInited;
-(NSString *)dataFilePath;
-(void)saveOverlay:(NSString *)overlayStarts;
-(void)saveColorMode:(NSString *)color;
-(void)initData;
-(void)resetData;

@end
