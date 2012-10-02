//
//  BlackMagicViewController.h
//  BlackMagic
//
//  Created by Michael Stratford on 12/03/2010.
//

#import <UIKit/UIKit.h>
#import <AVFoundation/AVFoundation.h>
#import "ImageUtils.h"

#define kMinimumGestureLength 100
#define kMaximumVariance 5

typedef enum {
	kNoSwipe = 0,
	kHorizontalSwipe,
	kVerticalSwipe
} SwipeType;

@class InstructionsController;

@interface BlackMagicViewController : UIViewController<AVCaptureVideoDataOutputSampleBufferDelegate, UIAccelerometerDelegate> {
	NSTimer * timer;
	AVCaptureSession *session;
	UIImageView *yesItIs;
	UIImageView *noItIsnt;
	UIView *previewView;
	UIView *artView;
	Image *imageToProcess;
	int upnext;
	int overlay_on;
	int currentPercentage;
	int currentCM;
	UILabel *percentageText;
	UIImageView *focusArea;
	UIButton *instructionsButton;
}
@property int upnext;
@property int overlay_on;
@property int currentPercentage;
@property int currentCM;
@property (nonatomic, retain) NSTimer *timer;
@property (nonatomic, retain) IBOutlet UIImageView *yesItIs;
@property (nonatomic, retain) IBOutlet UIImageView *noItIsnt;
@property (nonatomic, retain) IBOutlet UIView *previewView;
@property (nonatomic, retain) IBOutlet UIView *artView;
@property (nonatomic, retain) IBOutlet UILabel *percentageText;
@property (nonatomic, retain) IBOutlet UIImageView *focusArea;
@property (nonatomic, retain) IBOutlet UIButton *instructionsButton;
-(IBAction)instructionsButtonPressed;
-(IBAction)isItButtonPressed;
-(void)startCameraCapture;
-(void)stopCameraCapture;
-(void)overlayOn;
-(void)overlayOff;
@end

