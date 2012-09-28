#import "cocos2d.h"
#import "CCPanZoomController.h"
#import "EditMissionViewController.h"

@interface EditMissionScene : CCLayer <UIAlertViewDelegate>
{
    CCPanZoomController *_controller;
    CCLayer *gameLayer;
    NSMutableArray *planets;
    NSMutableArray *wells;
    bool gravityField;
    EditMissionViewController *_editMissionViewController;
}
@property (nonatomic,retain) CCLayer *gameLayer;
@property (nonatomic,retain) NSMutableArray *planets;
@property (nonatomic,retain) NSMutableArray *wells;
@property (readwrite) bool gravityField;
@property (retain) EditMissionViewController *editMissionViewController;

+(id) sceneWithId:(int)theId viewController:(EditMissionViewController *)viewController;
-(void) addPlanetWithRadius:(double)radius withDensity:(double)density withMoon:(bool)hasMoon;
-(void) addAntiGravityWithRadius:(double)radius withDensity:(double)density;

@end