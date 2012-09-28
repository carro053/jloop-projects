#import "EditMissionScene.h"
#import "CCPlanet.h"
#import "CCItem.h"
#import "CCGravityField.h"
#import "SBJson.h"
#import "UIDevice+IdentifierAddition.h"
#import "Constants.h"


int missionId;
bool rotatingMoon;
bool panning;
UITouch *panningTouch;
int serverId;
bool submitMissionCheck;
CCSprite *space;
CCSprite *selectedSprite;
CCSprite *start;
CCSprite *end;
CCPlanet *selectedPlanet;
CCLabelTTF *totalFuelLabel;

NSArray *editFilePaths;
NSString *editFileDirectory;
NSString *editFileName;

NSMutableArray *astronautsData;
NSMutableArray *astronauts;
NSMutableArray *items;
NSMutableArray *itemsData;
NSMutableArray *planetsData;
NSMutableArray *wellsData;
int total_fuel;
bool startup;

@implementation EditMissionScene

@synthesize gameLayer;
@synthesize wells;
@synthesize planets;
@synthesize gravityField;

@synthesize editMissionViewController = _editMissionViewController;

+(id) sceneWithId:(int)theId viewController:viewController
{
    missionId = theId;
    CCScene *scene = [CCScene node];
    
    EditMissionScene *layer = [EditMissionScene node];
    layer.editMissionViewController = viewController;
    [scene addChild: layer];
    
    return scene;
}

-(id) init
{
    if( (self=[super init] )) {
        
        BOOL iPad = NO;
#ifdef UI_USER_INTERFACE_IDIOM
        iPad = (UI_USER_INTERFACE_IDIOM() == UIUserInterfaceIdiomPad);
#endif
        double width = [[UIScreen mainScreen] bounds].size.width;
        double height = [[UIScreen mainScreen] bounds].size.height;
        [CCTexture2D PVRImagesHavePremultipliedAlpha:YES];
        [[CCSpriteFrameCache sharedSpriteFrameCache] addSpriteFramesWithFile:@"general.plist"];
        
        [self setIsTouchEnabled:YES];
        
        
        
        gameLayer = [CCLayer node];[gameLayer setContentSize:CGSizeMake(height, width - 88)];
        [self addChild:gameLayer z:1];
        

        
        space = [CCSprite spriteWithFile:@"stars.jpg"];
        space.anchorPoint = ccp(0.5,0.5);
        space.position = ccp(512,384);
        [gameLayer addChild:space z:0];
        
        // the pan/zoom controller
        _controller = [[CCPanZoomController controllerWithNode:gameLayer] retain];
        if(iPad)
        {
            _controller.zoomInLimit = 4.0f;
        }else{
            _controller.zoomInLimit = 2.0f;
        }
        CGRect windowRect = CGRectMake(0, 0, height, width - 88);
        [_controller setWindowRect:windowRect];
        CGRect boundingRect = CGRectMake(0, 0, 1024, 724);
        [_controller setBoundingRect:boundingRect];
        [_controller updatePosition:CGPointMake(512, 340)];
        [_controller optimalZoomOutLimit];
        _controller.zoomOutLimit = [_controller optimalZoomOutLimit];
        _controller.zoomRate = 1/200.0f;
        [_controller zoomOutOnPoint:CGPointMake(512,340) duration:0];
        startup = YES;
        
        [_controller enableWithTouchPriority:0 swallowsTouches:NO];
        panning = YES;
        
        
        CCGravityField *gravityFieldLayer = [CCGravityField layerWithParent:self];
        [gameLayer addChild:gravityFieldLayer z:1];
        
		astronauts = [[NSMutableArray alloc] init];
		items = [[NSMutableArray alloc] init];
		wells = [[NSMutableArray alloc] init];
		planets = [[NSMutableArray alloc] init];
        editFilePaths = NSSearchPathForDirectoriesInDomains (NSDocumentDirectory, NSUserDomainMask, YES);
        editFileDirectory = [[NSString alloc] initWithFormat:@"%@",[editFilePaths objectAtIndex:0]];
        editFileName = [[NSString alloc] initWithFormat:@"%@/CustomMission_%d.plist", editFileDirectory, missionId];
        //read plist
        NSMutableDictionary *dict = [NSMutableDictionary dictionaryWithContentsOfFile:editFileName];
        NSArray *keys = [dict allKeys];
        //loop through keys of plist
        NSArray *startData = [NSArray arrayWithObjects:[NSNumber numberWithDouble:240.0],[NSNumber numberWithDouble:160.0],nil];
        NSArray *endData = [NSArray arrayWithObjects:[NSNumber numberWithDouble:784.0],[NSNumber numberWithDouble:558.0],nil];
        serverId = 0;
        wellsData = [[NSMutableArray alloc] init];
        for(NSString *key in keys)
        {
            if([key isEqualToString:@"astronauts"])
                astronautsData = [[NSMutableArray alloc] initWithArray:[dict objectForKey:key]];
            if([key isEqualToString:@"items"])
                itemsData = [[NSMutableArray alloc] initWithArray:[dict objectForKey:key]];
            if([key isEqualToString:@"wells"])
                wellsData = [[dict objectForKey:key] retain];
            if([key isEqualToString:@"planets"])
                planetsData = [[NSMutableArray alloc] initWithArray:[dict objectForKey:key]];
            if([key isEqualToString:@"total_fuel"])
                total_fuel = [[dict objectForKey:key] intValue];
            if([key isEqualToString:@"startPoint"])
                startData = [dict objectForKey:key];
            if([key isEqualToString:@"endPoint"])
                endData = [dict objectForKey:key];
            if([key isEqualToString:@"server_id"])
                serverId = [[dict objectForKey:key] intValue];
        }
        [self.editMissionViewController.fuelSlider setValue:(total_fuel - minFuel) / (maxFuel - minFuel)];
        self.editMissionViewController.fuelText.title = [NSString stringWithFormat:@"%dkg",total_fuel];
        
        [self setupAstronauts];
        [self setupItems];
        [self setupWells];
        [self setupPlanets];
        gravityField = NO;
        
        
        start = [CCSprite spriteWithSpriteFrameName:@"space_station.png"];
        start.position = ccp([[startData objectAtIndex:0] doubleValue],[[startData objectAtIndex:1] doubleValue]);
        start.scale = 2.0;
        start.tag = 7;
        [gameLayer addChild:start z:50];
        end = [CCSprite spriteWithSpriteFrameName:@"space_station.png"];
        end.position = ccp([[endData objectAtIndex:0] doubleValue],[[endData objectAtIndex:1] doubleValue]);
        end.scale = 2.0;
        end.tag = 7;
        [gameLayer addChild:end z:50];
        
    }
    return self;
}

-(void) draw {
    if(startup)
    {
        [_controller zoomOutOnPoint:CGPointMake(512,340) duration:0.001];
        startup = NO;
    }
    if(self.editMissionViewController.cancel)
    {
        self.editMissionViewController.cancel = NO;
        [self cancelMission];
    }
    if(self.editMissionViewController.save)
    {
        self.editMissionViewController.save = NO;
        [self saveMission];
    }
    if(self.editMissionViewController.well)
    {
        self.editMissionViewController.well = NO;
        [self addWell];
    }
    if(self.editMissionViewController.fuel)
    {
        self.editMissionViewController.fuel = NO;
        [self addFuel];
    }
    if(self.editMissionViewController.astronaut)
    {
        self.editMissionViewController.astronaut = NO;
        [self addAstronaut];
    }
    if(self.editMissionViewController.antiGravity)
    {
        self.editMissionViewController.antiGravity = NO;
        [self addAntiGravityWithRadius:(self.editMissionViewController.antiGravityRadius.value * (maxRadius - minRadius) + minRadius) withDensity:(self.editMissionViewController.antiGravityDensity.value * (maxDensity - minDensity) + minDensity)];
    }
    if(self.editMissionViewController.planet)
    {
        self.editMissionViewController.planet = NO;
        [self addPlanetWithRadius:(self.editMissionViewController.planetRadius.value * (maxRadius - minRadius) + minRadius) withDensity:(self.editMissionViewController.planetDensity.value * (maxDensity - minDensity) + minDensity) withMoon:self.editMissionViewController.planetMoon.on];
    }
    if(self.editMissionViewController.gravityField)
    {
        self.editMissionViewController.gravityField = NO;
        [self toggleGravityField];
    }
    [super draw];
}

-(void) cancelMission {
    NSMutableDictionary *missionsdict = [NSMutableDictionary dictionaryWithContentsOfFile:editFileName];
    if([[missionsdict objectForKey:@"isNew"] boolValue])
    {
        NSError *error;
        if(![[NSFileManager defaultManager] removeItemAtPath:editFileName error:&error])
        {
            NSLog(@"Error deleting file");
        }
        
        
        NSArray *baseFilePaths = NSSearchPathForDirectoriesInDomains (NSDocumentDirectory, NSUserDomainMask, YES);
        NSString *baseFileDirectory = [NSString stringWithFormat:@"%@",[baseFilePaths objectAtIndex:0]];
        NSString *baseFileName = [NSString stringWithFormat:@"%@/CustomMissions.plist", baseFileDirectory];
        NSMutableDictionary *missionsdict = [NSMutableDictionary dictionaryWithContentsOfFile:baseFileName];
        NSMutableArray *missionArray = [missionsdict objectForKey:@"missionArray"];
        [missionArray removeObject:[NSNumber numberWithInt:missionId]];
        [missionsdict setObject:missionArray forKey:@"missionArray"];
        if(![missionsdict writeToFile:baseFileName atomically:NO])
            NSLog(@"failed to write plist");
    }
    //[[CCDirector sharedDirector] replaceScene:[CreateMenuScene scene]];
	[self.editMissionViewController dismissModalViewControllerAnimated:YES];
}

-(void) toggleGravityField {
    gravityField = !gravityField;
    if(gravityField)
    {
        self.editMissionViewController.fidoButton.title = @"Hide F.I.D.O.";
    }else{
        self.editMissionViewController.fidoButton.title = @"Show F.I.D.O.";
    }
}
-(void) addPlanetWithRadius:(double)radius withDensity:(double)density withMoon:(bool)hasMoon
{
    CCPlanet *planet = [CCPlanet spriteWithFile:@"planet.png" withRadius:radius withDensity:density withAntiGravity:NO withMoon:hasMoon withMoonAngle:0.0];
    planet.position = ccp(512, 362);
    planet.radius = radius;
    planet.density = density;
    planet.antiGravity = NO;
    planet.hasMoon = hasMoon;
    planet.scale = radius/70.0;
    planet.tag = 2;
    [gameLayer addChild:planet z:1];
    [planets addObject:planet];
}
-(void) addAntiGravityWithRadius:(double)radius withDensity:(double)density
{
    CCPlanet *planet = [CCPlanet spriteWithFile:@"planet.png" withRadius:radius withDensity:density withAntiGravity:YES withMoon:NO withMoonAngle:0.0];
    planet.position = ccp(512, 362);
    planet.radius = radius;
    planet.density = density;
    planet.antiGravity = YES;
    planet.hasMoon = NO;
    planet.scale = radius/70.0;
    planet.tag = 2;
    [gameLayer addChild:planet z:1];
    [planets addObject:planet];
}

-(void) addAstronaut {
    CCSprite *astronaut = [CCSprite spriteWithSpriteFrameName:@"astronaut.png"];
    astronaut.position = ccp(512,362);
    [gameLayer addChild:astronaut z:2];
    [astronaut setRotation:arc4random() % 360];
    astronaut.tag = 1;
    [astronauts addObject:astronaut];
}

-(void) addFuel {
    CCItem *fuel = [CCItem spriteWithSpriteFrameName:@"fuel.png" andType:@"Fuel"];
    fuel.position = ccp(512,362);
    [gameLayer addChild:fuel z:2];
    [fuel setRotation:arc4random() % 360];
    [items addObject:fuel];
}

-(void) addWell {
    CCSprite *well = [CCSprite spriteWithSpriteFrameName:@"gravity_well.png"];
    well.position = ccp(512,362);
    well.tag = 3600;
    [gameLayer addChild:well z:3];
    [well setRotation:arc4random() % 360];
    [wells addObject:well];
}

-(void) setupAstronauts {
    for(NSDictionary *astro in astronautsData)
    {
        CCSprite *astronaut = [CCSprite spriteWithSpriteFrameName:@"astronaut.png"];
        astronaut.position = ccp([[astro objectForKey:@"x"] doubleValue],[[astro objectForKey:@"y"] doubleValue]);
        [gameLayer addChild:astronaut z:2];
        [astronaut setRotation:arc4random() % 360];
        astronaut.tag = 1;
        [astronauts addObject:astronaut];
    }
}
-(void) setupItems {
    for(NSDictionary *theItem in itemsData)
    {
        CCItem *item;
        if([[theItem objectForKey:@"type"] isEqual:@"Fuel"])
        {
            item = [CCItem spriteWithSpriteFrameName:@"fuel.png" andType:@"Fuel"];
        }
        item.position = ccp([[theItem objectForKey:@"x"] doubleValue],[[theItem objectForKey:@"y"] doubleValue]);
        [gameLayer addChild:item z:2];
        [item setRotation:arc4random() % 360];
        [items addObject:item];
    }
}

-(void) setupWells {
    for(NSDictionary *theWell in wellsData)
    {
        CCSprite *well = [CCSprite spriteWithSpriteFrameName:@"gravity_well.png"];
        well.position = ccp([[theWell objectForKey:@"x"] doubleValue],[[theWell objectForKey:@"y"] doubleValue]);
        well.tag = [[theWell objectForKey:@"power"] intValue];
        [gameLayer addChild:well z:2];
        [well setRotation:arc4random() % 360];
        [wells addObject:well];
    }
}
-(void) setupPlanets {
    for(NSDictionary *plan in planetsData)
    {
        CCPlanet *planet = [CCPlanet spriteWithFile:@"planet.png" withRadius:[[plan objectForKey:@"radius"] doubleValue] withDensity:[[plan objectForKey:@"density"] doubleValue] withAntiGravity:[[plan objectForKey:@"antiGravity"] boolValue] withMoon:[[plan objectForKey:@"hasMoon"] boolValue] withMoonAngle:[[plan objectForKey:@"moonAngle"] doubleValue]];
        planet.position = ccp([[plan objectForKey:@"x"] doubleValue],[[plan objectForKey:@"y"] doubleValue]);
        planet.radius = [[plan objectForKey:@"radius"] doubleValue];
        planet.density = [[plan objectForKey:@"density"] doubleValue];
        planet.antiGravity = [[plan objectForKey:@"antiGravity"] boolValue];
        planet.hasMoon = [[plan objectForKey:@"hasMoon"] boolValue];
        planet.moonAngle = [[plan objectForKey:@"moonAngle"] doubleValue];
        planet.scale = [[plan objectForKey:@"radius"] doubleValue] / 70.0;
        planet.tag = 2;
        [planets addObject:planet];
        [gameLayer addChild:planet z:1];
    }
}
-(void) ccTouchesBegan:(NSSet *)touches withEvent:(UIEvent *)event
{
    if(selectedSprite == nil)
    {
        rotatingMoon = NO;
        UITouch *touch = [touches anyObject];
        CGPoint touchLocation = [touch locationInView:[touch view]];
        touchLocation = [[CCDirector sharedDirector] convertToGL:touchLocation];
        touchLocation = [gameLayer convertToNodeSpace:touchLocation];
        
        selectedSprite = nil;
        selectedPlanet = nil;
        for (CCSprite *sprite in astronauts) {
            if(sprite.visible && pow(touchLocation.x-sprite.position.x,2) + pow(touchLocation.y - sprite.position.y,2) < pow(25.0,2))
            {
                selectedSprite = sprite;
                break;
            }
        }
        if(selectedSprite == nil)
        {
            for (CCSprite *sprite in wells) {
                if(sprite.visible && pow(touchLocation.x-sprite.position.x,2) + pow(touchLocation.y - sprite.position.y,2) < pow(32.0,2))
                {
                    selectedSprite = sprite;
                    break;
                }
            }
        }
        if(selectedSprite == nil)
        {
            for (CCSprite *sprite in items) {
                if(sprite.visible && pow(touchLocation.x-sprite.position.x,2) + pow(touchLocation.y - sprite.position.y,2) < pow(25.0,2))
                {
                    selectedSprite = sprite;
                    break;
                }
            }
        }
        if(selectedSprite == nil)
        {
            for (CCPlanet *sprite in planets) {
                if (sprite.visible && pow(touchLocation.x-sprite.position.x,2) + pow(touchLocation.y - sprite.position.y,2) < pow(sprite.radius,2)) {            
                    selectedSprite = sprite;
                    break;
                }
            }
        }
        if(selectedSprite == nil)
        {
            for (CCPlanet *sprite in planets) {
                if(sprite.visible && sprite.hasMoon)
                {
                    if (pow(touchLocation.x-(sprite.position.x + cos(sprite.moonAngle) * sprite.radius * sprite.xOrbit),2) + pow(touchLocation.y - (sprite.position.y + sin(sprite.moonAngle) * sprite.radius * sprite.yOrbit),2) < pow(sprite.radius / 2,2)) {
                        selectedSprite = sprite;
                        selectedPlanet = sprite;
                        rotatingMoon = YES;
                        break;
                    }
                }
            }
        }
        
        if(selectedSprite == nil)
        {
            if (pow(touchLocation.x-start.position.x,2) + pow(touchLocation.y - start.position.y,2) < pow(20,2)) {            
                selectedSprite = start;
            }
        }
        if(selectedSprite == nil)
        {
            if (pow(touchLocation.x-end.position.x,2) + pow(touchLocation.y - end.position.y,2) < pow(20,2)) {            
                selectedSprite = end;
            }
        }
        if(selectedSprite != nil)
        {
            [_controller disable];
            [_controller._touches removeObject:touch];
            panning = NO;
            panningTouch = touch;
        }
    }
}
-(void) ccTouchesMoved:(NSSet *)touches withEvent:(UIEvent *)event
{
    UITouch *touch = [touches anyObject];
    CGPoint touchLocation = [touch locationInView:[touch view]];
    touchLocation = [[CCDirector sharedDirector] convertToGL:touchLocation];
    touchLocation = [gameLayer convertToNodeSpace:touchLocation];
    if (selectedSprite && [touch isEqual:panningTouch]) {
        if(rotatingMoon)
        {
            double angle = atan((touchLocation.y - selectedSprite.position.y) / (touchLocation.x - selectedSprite.position.x));
            if(touchLocation.x - selectedSprite.position.x < 0) angle += M_PI;
            selectedPlanet.moonAngle = angle;
            
        }else{
            selectedSprite.position = touchLocation;
            if(selectedSprite.position.y > 724)
                selectedSprite.position = CGPointMake(selectedSprite.position.x, 724);
            if(selectedSprite.position.y < 0)
                selectedSprite.position = CGPointMake(selectedSprite.position.x, 0);
            if(selectedSprite.position.x > 1024)
                selectedSprite.position = CGPointMake(1024, selectedSprite.position.y);
            if(selectedSprite.position.x < 0)
                selectedSprite.position = CGPointMake(0, selectedSprite.position.y);
        }
    }
}

-(void) ccTouchesEnded:(NSSet *)touches withEvent:(UIEvent *)event
{
    UITouch *touch = [touches anyObject];
    //CGPoint touchLocation = [touch locationInView:[touch view]];
    //touchLocation = [[CCDirector sharedDirector] convertToGL:touchLocation];
    //touchLocation = [gameLayer convertToNodeSpace:touchLocation];
    
    if(!panning && [touch isEqual:panningTouch])
    {
        if(touch.tapCount > 1) {
            if(selectedSprite.tag != 7)
                selectedSprite.visible = NO;
        }
        panning = YES;
        selectedSprite = nil;
        [_controller enableWithTouchPriority:0 swallowsTouches:NO];
    }
    [_controller._touches removeAllObjects];
}

-(void) saveMission {
    if(gravityField)
        gravityField = !gravityField;
    _controller.zoomRate = 10.0;
    [_controller zoomOnPoint:CGPointMake(512, 340) duration:0.0001 scale:_controller.zoomOutLimit];
    [totalFuelLabel setString:@""];
    submitMissionCheck = NO;
    [self runAction: [CCSequence actions:[CCDelayTime actionWithDuration: 0.1],[CCCallFunc actionWithTarget:self selector:@selector(actuallySaveMission)],nil]];
}




-(void) actuallySaveMission {
    [[CCTextureCache sharedTextureCache] removeAllTextures];
    //UIImage *screenshot = [[CCDirector sharedDirector] screenshotUIImage];
    CCScene *scene = [[CCDirector sharedDirector] runningScene];
    CCNode *n = [scene.children objectAtIndex:0];
    [CCDirector sharedDirector].nextDeltaTimeZero = YES;
    
    CGSize winSize = [CCDirector sharedDirector].winSize;
    CCRenderTexture* rtx =
    [CCRenderTexture renderTextureWithWidth:winSize.width
                                     height:winSize.height];
    [rtx begin];
    [n visit];
    [rtx end];
    
    UIImage *screenshot = [rtx getUIImageFromBuffer];
    
    UIImage *image_hd = [self imageByScalingAndCroppingImage:screenshot ForSize:CGSizeMake(200.0, 200.0)];
    [UIImageJPEGRepresentation(image_hd, 1.0) writeToFile:[NSString stringWithFormat:@"%@/CustomMission_%d@2x.jpg", editFileDirectory, missionId] atomically:YES];
    //UIImage *icon_border_hd = [UIImage imageNamed:@"icon_border-hd.jpg"];
    //[UIImageJPEGRepresentation([self addImage:icon_border_hd toImage:image_hd], 1.0) writeToFile:[NSString stringWithFormat:@"%@/CustomMission_%d-hd.jpg", editFileDirectory, missionId] atomically:YES];
    UIImage *image = [self imageByScalingAndCroppingImage:screenshot ForSize:CGSizeMake(100.0, 100.0)];
    [UIImageJPEGRepresentation(image, 1.0) writeToFile:[NSString stringWithFormat:@"%@/CustomMission_%d.jpg", editFileDirectory, missionId] atomically:YES];
    //UIImage *icon_border = [UIImage imageNamed:@"icon_border.jpg"];
    //[UIImageJPEGRepresentation([self addImage:icon_border toImage:image], 1.0) writeToFile:[NSString stringWithFormat:@"%@/CustomMission_%d.jpg", editFileDirectory, missionId] atomically:YES];
    
    NSMutableArray *saveAstronauts = [[NSMutableArray alloc] init];
    
    for (CCSprite *sprite in astronauts) {
        if(sprite.visible)
            [saveAstronauts addObject:[NSDictionary dictionaryWithObjects:[NSArray arrayWithObjects:[NSNumber numberWithDouble:sprite.position.x],[NSNumber numberWithDouble:sprite.position.y], nil] forKeys:[NSArray arrayWithObjects:@"x",@"y",nil]]];
    }
    
    NSMutableArray *saveWells = [[NSMutableArray alloc] init];
    
    for (CCSprite *sprite in wells) {
        if(sprite.visible)
            [saveWells addObject:[NSDictionary dictionaryWithObjects:[NSArray arrayWithObjects:[NSNumber numberWithInt:sprite.tag],[NSNumber numberWithDouble:sprite.position.x],[NSNumber numberWithDouble:sprite.position.y], nil] forKeys:[NSArray arrayWithObjects:@"power",@"x",@"y",nil]]];
    }
    
    NSMutableArray *saveItems = [[NSMutableArray alloc] init];
    
    for (CCItem *sprite in items) {
        if(sprite.visible)
            [saveItems addObject:[NSDictionary dictionaryWithObjects:[NSArray arrayWithObjects:sprite.itemType,[NSNumber numberWithDouble:sprite.position.x],[NSNumber numberWithDouble:sprite.position.y], nil] forKeys:[NSArray arrayWithObjects:@"type",@"x",@"y",nil]]];
    }
    
    NSMutableArray *savePlanets = [[NSMutableArray alloc] init];
    
    for (CCPlanet *sprite in planets) {
        if(sprite.visible)
            [savePlanets addObject:[NSDictionary dictionaryWithObjects:[NSArray arrayWithObjects:[NSNumber numberWithDouble:sprite.position.x],[NSNumber numberWithDouble:sprite.position.y],[NSNumber numberWithDouble:sprite.radius],[NSNumber numberWithDouble:sprite.density],[NSNumber numberWithBool:sprite.antiGravity],[NSNumber numberWithBool:sprite.hasMoon],[NSNumber numberWithDouble:sprite.moonAngle], nil] forKeys:[NSArray arrayWithObjects:@"x",@"y",@"radius",@"density",@"antiGravity",@"hasMoon",@"moonAngle",nil]]];
    }
    
    NSArray *startData = [NSArray arrayWithObjects:[NSNumber numberWithDouble:start.position.x],[NSNumber numberWithDouble:start.position.y],nil];
    NSArray *endData = [NSArray arrayWithObjects:[NSNumber numberWithDouble:end.position.x],[NSNumber numberWithDouble:end.position.y],nil];
    
    NSMutableDictionary *missionsdict = [NSMutableDictionary dictionaryWithContentsOfFile:editFileName];
    [missionsdict setObject:saveItems forKey:@"items"];
    [missionsdict setObject:saveWells forKey:@"wells"];
    [missionsdict setObject:saveAstronauts forKey:@"astronauts"];
    [missionsdict setObject:savePlanets forKey:@"planets"];
    total_fuel = round(minFuel + 10 * round(self.editMissionViewController.fuelSlider.value * (maxFuel - minFuel) / 10));
    [missionsdict setObject:[NSNumber numberWithInt:total_fuel] forKey:@"total_fuel"];
    [missionsdict setObject:startData forKey:@"startPoint"];
    [missionsdict setObject:endData forKey:@"endPoint"];
    [missionsdict setObject:[NSNumber numberWithBool:NO] forKey:@"solved"];
    [missionsdict setObject:[NSNumber numberWithBool:NO] forKey:@"isNew"];
    if(![missionsdict writeToFile:editFileName atomically:NO])
        NSLog(@"failed to write plist");
	NSString *deviceUDID = [[UIDevice currentDevice] uniqueGlobalDeviceIdentifier];
    [missionsdict setObject:deviceUDID forKey:@"device_id"];
    [saveAstronauts release];
    [saveWells release];
    [saveItems release];
    [savePlanets release];
    //[[CCDirector sharedDirector] replaceScene:[CreateMenuScene scene]];
	[self.editMissionViewController dismissModalViewControllerAnimated:YES];
}

- (void)connection:(NSURLConnection *)connection didFailWithError:(NSError *)error {
    NSLog(@"Error: %@", error);
    NSLog(@"OFFLINE");
    //[[CCDirector sharedDirector] replaceScene:[CreateMenuScene scene]];
	[self.editMissionViewController dismissModalViewControllerAnimated:YES];
}

- (UIImage*)imageByScalingAndCroppingImage:(UIImage *)sourceImage ForSize:(CGSize)targetSize
{
    UIImage *newImage = nil;        
    CGSize imageSize = sourceImage.size;
    CGFloat width = imageSize.width;
    CGFloat height = imageSize.height;
    CGFloat targetWidth = targetSize.width;
    CGFloat targetHeight = targetSize.height;
    CGFloat scaleFactor = 0.0;
    CGFloat scaledWidth = targetWidth;
    CGFloat scaledHeight = targetHeight;
    CGPoint thumbnailPoint = CGPointMake(0.0,0.0);
    
    if (CGSizeEqualToSize(imageSize, targetSize) == NO) 
    {
        CGFloat widthFactor = targetWidth / width;
        CGFloat heightFactor = targetHeight / height;
        
        if (widthFactor > heightFactor) 
            scaleFactor = widthFactor; // scale to fit height
        else
            scaleFactor = heightFactor; // scale to fit width
        scaledWidth  = width * scaleFactor;
        scaledHeight = height * scaleFactor;
        
        // center the image
        if (widthFactor > heightFactor)
        {
            thumbnailPoint.y = (targetHeight - scaledHeight) * 0.5; 
        }
        else 
            if (widthFactor < heightFactor)
            {
                thumbnailPoint.x = (targetWidth - scaledWidth) * 0.5;
            }
    }       
    
    UIGraphicsBeginImageContext(targetSize); // this will crop
    
    CGRect thumbnailRect = CGRectZero;
    thumbnailRect.origin = thumbnailPoint;
    thumbnailRect.size.width  = scaledWidth;
    thumbnailRect.size.height = scaledHeight;
    
    [sourceImage drawInRect:thumbnailRect];
    
    newImage = UIGraphicsGetImageFromCurrentImageContext();
    if(newImage == nil) 
        NSLog(@"could not scale image");
    
    //pop the context to get back to the default
    UIGraphicsEndImageContext();
    return newImage;
}

- (UIImage *)addImage:(UIImage *)image1 toImage:(UIImage *)image2 {
	UIGraphicsBeginImageContext(image1.size);
    
	// Draw image1
	[image1 drawInRect:CGRectMake(0, 0, image1.size.width, image1.size.height)];
    
	// Draw image2
	[image2 drawInRect:CGRectMake((image1.size.width - image2.size.width) / 2, (image1.size.height - image2.size.height) / 2, image2.size.width, image2.size.height)];
    
	UIImage *resultingImage = UIGraphicsGetImageFromCurrentImageContext();
    
	UIGraphicsEndImageContext();
    
	return resultingImage;
}

- (void)onExit {
    [_controller disable];
    [_controller release];
    
    [astronauts release];
    [items release];
    [wells release];
    [planets release];
    [astronautsData release];
    [itemsData release];
    [wellsData release];
    [planetsData release];
    [editFileDirectory release];
    [editFileName release];
    [self removeAllChildrenWithCleanup:YES];
    [self stopAllActions];
    [self unscheduleAllSelectors];
    [[CCTouchDispatcher sharedDispatcher] removeAllDelegates];
    [[CCTextureCache sharedTextureCache] removeAllTextures];
    [super onExit];
}


- (void) dealloc
{    
    [super dealloc];
}
@end