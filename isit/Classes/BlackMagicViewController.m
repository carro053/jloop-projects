//
//  BlackMagicViewController.m
//  BlackMagic
//
//  Created by Michael Stratford on 12/03/2010.
//

#import "BlackMagicViewController.h"
#import "ImageUtils.h"
#import "InstructionsController.h"
#import "SettingsTracker.h"

@interface BlackMagicViewController()

- (void)startCameraCapture;
- (void)stopCameraCapture;
- (void)overlayOn;

@end


@implementation BlackMagicViewController
@synthesize timer;
@synthesize previewView;
@synthesize artView;
@synthesize yesItIs;
@synthesize noItIsnt;
@synthesize upnext;
@synthesize overlay_on;
@synthesize currentPercentage;
@synthesize percentageText;
@synthesize focusArea;
@synthesize instructionsButton;
@synthesize currentCM;


- (void)viewWillAppear:(BOOL)animated
{
	SettingsTracker *settings = [[SettingsTracker alloc] init];
	[settings initData];
	UIImage *img = [UIImage imageWithContentsOfFile: [[NSBundle mainBundle] pathForResource:[NSString stringWithFormat:@"focus_area_%d",[settings.colorMode intValue]] ofType:@"png"]];
	currentCM = [settings.colorMode intValue];
	[focusArea setImage:img];
	[settings release];
}
// Implement viewDidLoad to do additional setup after loading the view, typically from a nib.
- (void)viewDidLoad {
	upnext = 0;
	currentPercentage = 0;
	overlay_on = 0;
	yesItIs.hidden = YES;
	noItIsnt.hidden = YES;
	instructionsButton.hidden = YES;
	percentageText.hidden = YES;
	focusArea.hidden = YES;
    [super viewDidLoad];
	UISwipeGestureRecognizer *downrecognizer;
    downrecognizer = [[UISwipeGestureRecognizer alloc] initWithTarget:self action:@selector(handleSwipeFrom:)];
    downrecognizer.numberOfTouchesRequired=2;
	downrecognizer.direction=UISwipeGestureRecognizerDirectionDown;
    [[self view] addGestureRecognizer:downrecognizer];
    [downrecognizer release]; 
	UISwipeGestureRecognizer *rightrecognizer;
    rightrecognizer = [[UISwipeGestureRecognizer alloc] initWithTarget:self action:@selector(handleRightSwipeFrom:)];
    rightrecognizer.numberOfTouchesRequired=1;
	rightrecognizer.direction=UISwipeGestureRecognizerDirectionRight;
    [[self view] addGestureRecognizer:rightrecognizer];
    [rightrecognizer release]; 
	UISwipeGestureRecognizer *leftrecognizer;
    leftrecognizer = [[UISwipeGestureRecognizer alloc] initWithTarget:self action:@selector(handleLeftSwipeFrom:)];
    leftrecognizer.numberOfTouchesRequired=1;
	leftrecognizer.direction=UISwipeGestureRecognizerDirectionLeft;
    [[self view] addGestureRecognizer:leftrecognizer];
    [leftrecognizer release]; 
    [super viewDidLoad];
}
-(void)handleSwipeFrom:(UISwipeGestureRecognizer *)recognizer {
	if(overlay_on == 1)
	{
		[self overlayOff];
	}else{
		[self overlayOn];
	}
}
-(void)handleRightSwipeFrom:(UISwipeGestureRecognizer *)recognizer {
	if(currentCM == 4)
	{
		currentCM = 0;
	}else{
		currentCM = currentCM+1;
	}
	UIImage *img = [UIImage imageWithContentsOfFile: [[NSBundle mainBundle] pathForResource:[NSString stringWithFormat:@"focus_area_%d",currentCM] ofType:@"png"]];
	[focusArea setImage:img];
}
-(void)handleLeftSwipeFrom:(UISwipeGestureRecognizer *)recognizer {
	if(currentCM == 0)
	{
		currentCM = 4;
	}else{
		currentCM = currentCM-1;
	}
	UIImage *img = [UIImage imageWithContentsOfFile: [[NSBundle mainBundle] pathForResource:[NSString stringWithFormat:@"focus_area_%d",currentCM] ofType:@"png"]];
	[focusArea setImage:img];
}

-(void)overlayOn
{
	instructionsButton.hidden = NO;
	percentageText.hidden = NO;
	focusArea.hidden = NO;
	overlay_on = 1;
}

-(void)overlayOff
{
	instructionsButton.hidden = YES;
	percentageText.hidden = YES;
	focusArea.hidden = YES;
	overlay_on = 0;
}

#pragma mark Camera Capture Control

- (void)startCameraCapture {
	NSLog(@"CStart");
	// start capturing frames
	// Create the AVCapture Session
	session = [[AVCaptureSession alloc] init];
	
	// create a preview layer to show the output from the camera
	AVCaptureVideoPreviewLayer *previewLayer = [AVCaptureVideoPreviewLayer layerWithSession:session];
	previewLayer.frame = previewView.frame;
	previewLayer.frame = CGRectMake(previewLayer.frame.origin.x, previewLayer.frame.origin.y - 17, previewLayer.frame.size.width,previewLayer.frame.size.height);
	[previewView.layer addSublayer:previewLayer];
	// Get the default camera device
	AVCaptureDevice* camera = [AVCaptureDevice defaultDeviceWithMediaType:AVMediaTypeVideo];
	
	// Create a AVCaptureInput with the camera device
	NSError *error=nil;
	AVCaptureInput* cameraInput = [[AVCaptureDeviceInput alloc] initWithDevice:camera error:&error];
	if (cameraInput == nil) {
		NSLog(@"Error to create camera capture:%@",error);
	}
	
	// Set the output
	AVCaptureVideoDataOutput* videoOutput = [[AVCaptureVideoDataOutput alloc] init];
	
	// create a queue to run the capture on
	dispatch_queue_t captureQueue=dispatch_queue_create("catpureQueue", NULL);
	
	// setup our delegate
	[videoOutput setSampleBufferDelegate:self queue:captureQueue];

	// configure the pixel format
	videoOutput.videoSettings = [NSDictionary dictionaryWithObjectsAndKeys:[NSNumber numberWithUnsignedInt:kCVPixelFormatType_32BGRA], (id)kCVPixelBufferPixelFormatTypeKey,
									 nil];

	// and the size of the frames we want
	[session setSessionPreset:AVCaptureSessionPresetMedium];

	// Add the input and output
	[session addInput:cameraInput];
	[session addOutput:videoOutput];
	// Start the session
	[session startRunning];
}

- (void)captureOutput:(AVCaptureOutput *)captureOutput didOutputSampleBuffer:(CMSampleBufferRef)sampleBuffer fromConnection:(AVCaptureConnection *)connection {
	// only run if we're not alupnext processing an image
	if(imageToProcess==NULL) {
		// this is the image buffer
		CVImageBufferRef cvimgRef = CMSampleBufferGetImageBuffer(sampleBuffer);
		// Lock the image buffer
		CVPixelBufferLockBaseAddress(cvimgRef,0);
		// access the data
		int width=CVPixelBufferGetWidth(cvimgRef);
		int height=CVPixelBufferGetHeight(cvimgRef);
		// get the raw image bytes
		uint8_t *buf=(uint8_t *) CVPixelBufferGetBaseAddress(cvimgRef);
		size_t bprow=CVPixelBufferGetBytesPerRow(cvimgRef);
		// turn it into something useful
		imageToProcess=createImage(buf, bprow, width, height,currentCM);
		// trigger the image processing on the main thread
		[self performSelectorOnMainThread:@selector(processImage) withObject:nil waitUntilDone:NO];
	}
}


-(void) stopCameraCapture {
	NSLog(@"CStop");
	[session stopRunning];
	[session release];
	session=nil;
}

#pragma mark -
#pragma mark Image processing

-(void) processImage {
	if(imageToProcess)
	{
		//find top left point for the area we want to look at
		int sx = ceil(imageToProcess->width*0.45);
		int sy = ceil(imageToProcess->height*0.45);
		//how big we want the area to be
		int wx = ceil(imageToProcess->width*0.10);
		int hy = ceil(imageToProcess->height*0.10);
		//start black pixel count at 0
		int darkness = 0;
		//total amount of pixels being looked at
		int total = wx*hy;
		int percentage = 0;
		if(currentCM == 0)
		{
			for(int y=sy; y<sy+hy; y++) {
				for(int x=sx; x<sx+wx; x++) {
					if(imageToProcess->pixels[y][x] < 70)
						++darkness;
				}
			}
			percentage = ceil(100*darkness/total);
		}else if(currentCM == 4)
		{
			for(int y=sy; y<sy+hy; y++) {
				for(int x=sx; x<sx+wx; x++) {
					if(imageToProcess->pixels[y][x] > 185)
						++darkness;
				}
			}
			percentage = ceil(100*darkness/total);
		}else{
			for(int y=sy; y<sy+hy; y++) {
				for(int x=sx; x<sx+wx; x++) {
					darkness = darkness + imageToProcess->pixels[y][x];
				}
			}
			percentage = ceil(darkness/total);
		}
		percentageText.text = [NSString stringWithFormat:@"%i%%", percentage];
		currentPercentage = percentage;
		destroyImage(imageToProcess);
		imageToProcess=NULL;
	}
}

-(IBAction)isItButtonPressed {
	if(yesItIs.hidden == YES && noItIsnt.hidden == YES)
	{
		if(upnext == 1)
		{
			yesItIs.hidden = NO;
			timer = [NSTimer scheduledTimerWithTimeInterval:1 target:self selector:@selector(hideYesResponse) userInfo:nil repeats:NO];
			upnext = 0;
		}else{
			if(currentPercentage > 49)
			{
				upnext = 1;
			}
			noItIsnt.hidden = NO;
			timer = [NSTimer scheduledTimerWithTimeInterval:1 target:self selector:@selector(hideNoResponse) userInfo:nil repeats:NO];
		}
	}
}


#pragma mark hideResponses
- (void)hideYesResponse {
	yesItIs.hidden = YES;
}

- (void)hideNoResponse {
	noItIsnt.hidden = YES;
}


-(IBAction)instructionsButtonPressed {
	if(overlay_on == 1)
	{
		SettingsTracker *settings = [[SettingsTracker alloc] init];
		[settings saveColorMode:[NSString stringWithFormat: @"%d", currentCM]];
		[settings release];
		InstructionsController *instructionsViewController = [[InstructionsController alloc] initWithNibName:@"InstructionsController" bundle:nil];
		[self presentModalViewController:instructionsViewController animated:YES];
		[instructionsViewController release];
	}
}

- (void)didReceiveMemoryWarning {
	// Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
	
	// Release any cached data, images, etc that aren't in use.
}

- (void)viewDidUnload {
	// Release any retained subviews of the main view.
	// e.g. self.myOutlet = nil;
	[self stopCameraCapture];
	self.previewView=nil;
}


- (void)dealloc {
	[self stopCameraCapture];
	self.previewView = nil;
	self.artView = nil;
	[percentageText release];
	[instructionsButton release];
	[focusArea release];
	[yesItIs release];
	[noItIsnt release];
	[timer release];

    [super dealloc];
}

@end
