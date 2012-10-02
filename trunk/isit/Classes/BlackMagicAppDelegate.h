//
//  BlackMagicAppDelegate.h
//  BlackMagic
//
//  Created by Michael Stratford on 12/03/2010.
//

#import <UIKit/UIKit.h>

@class BlackMagicViewController;

@interface BlackMagicAppDelegate : NSObject <UIApplicationDelegate> {
    UIWindow *window;
    BlackMagicViewController *blackMagicViewController;
}

@property (nonatomic, retain) IBOutlet UIWindow *window;
@property (nonatomic, retain) IBOutlet BlackMagicViewController *blackMagicViewController;

@end

