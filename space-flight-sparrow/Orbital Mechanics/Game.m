//
//  Game.m
//  AppScaffold
//

#import "Game.h"

float spaceWidth;
float spaceHeight;
float maxZoom;
float minZoom;
int missionId;

NSMutableArray *astronautsData;
NSMutableArray *beaconsData;
NSMutableArray *itemsData;
NSMutableArray *wellsData;
NSMutableArray *planetsData;
int movingPoint;
BOOL newPoint;
BOOL startFlying;
bool panning;
bool startup;
bool shipOutOfFuel;
UITouch *panningTouch;

double previousProbeX;
double previousProbeY;
double probeSpeed;
bool probeActive;
bool placingProbe;

NSArray *startData;
NSArray *endData;

NSDate *lastProbeMoveDate;
double lastProbeMoveSpeed;

// --- private interface ---------------------------------------------------------------------------

@interface Game ()

- (void)setup;

@end


// --- class implementation ------------------------------------------------------------------------

@implementation Game
@synthesize shipSpeed;
@synthesize previousShipX;
@synthesize previousShipY;
@synthesize previousPreviousShipX;
@synthesize previousPreviousShipY;
@synthesize shipFlightDuration;
@synthesize shipFlying;
@synthesize startFlying;
@synthesize outOfFuel;
@synthesize colorLine;
@synthesize astronauts;
@synthesize items;
@synthesize wells;
@synthesize planets;
@synthesize thePath;
@synthesize shipPath;
@synthesize pathPoints;
@synthesize total_fuel;
@synthesize fuel_cost;
@synthesize travelTime;
@synthesize gravityField;
@synthesize gameWidth  = mGameWidth;
@synthesize gameHeight = mGameHeight;

- (id)initWithWidth:(float)width height:(float)height
{
    if ((self = [super init]))
    {
        mGameWidth = width;
        mGameHeight = height;
        
        spaceWidth = 1024.0;
        spaceHeight = 724.0;
        
        if(mGameWidth / mGameHeight > spaceWidth / spaceHeight)
        {
            maxZoom = mGameWidth / spaceWidth;
        }else{
            maxZoom = mGameHeight / spaceHeight;
        }
        self.scaleX = self.scaleY = maxZoom;
        minZoom = 2.0;
        
        [self addEventListener:@selector(onEnterFrame:) atObject:self forType:SP_EVENT_TYPE_ENTER_FRAME];
        
        [self addEventListener:@selector(onTouchEvent:) atObject:self forType:SP_EVENT_TYPE_TOUCH];
        
        
        
        lastProbeMoveDate = [[NSDate alloc] init];
        
		astronauts = [[NSMutableArray alloc] init];
		items = [[NSMutableArray alloc] init];
		wells = [[NSMutableArray alloc] init];
		planets = [[NSMutableArray alloc] init];
		shipPath = [[NSMutableArray alloc] init];
		thePath = [[NSMutableArray alloc] init];
		pathPoints = [[NSMutableArray alloc] init];
        
        
        
        NSString* plistPath = [[NSBundle mainBundle] pathForResource:@"FlightSchool" ofType:@"plist"];
        NSDictionary *pdict = [NSDictionary dictionaryWithContentsOfFile:plistPath];
        NSMutableArray *missionArray = [pdict objectForKey:@"flightSchoolArray"];
        NSDictionary *dict = [missionArray objectAtIndex:missionId];
        
        startData = [NSArray arrayWithObjects:[NSNumber numberWithDouble:240.0],[NSNumber numberWithDouble:160.0],nil];
        endData = [NSArray arrayWithObjects:[NSNumber numberWithDouble:784.0],[NSNumber numberWithDouble:558.0],nil];
        //read plist
        NSArray *keys = [dict allKeys];
        //loop through keys of plist
        for(NSString *key in keys)
        {
            if([key isEqualToString:@"astronauts"])
                astronautsData = [[NSMutableArray alloc] initWithArray:[dict objectForKey:key]];
            if([key isEqualToString:@"beacons"])
                beaconsData = [[NSMutableArray alloc] initWithArray:[dict objectForKey:key]];
            if([key isEqualToString:@"items"])
                itemsData = [[NSMutableArray alloc] initWithArray:[dict objectForKey:key]];
            if([key isEqualToString:@"wells"])
                wellsData = [[NSMutableArray alloc] initWithArray:[dict objectForKey:key]];
            if([key isEqualToString:@"planets"])
                planetsData = [[NSMutableArray alloc] initWithArray:[dict objectForKey:key]];
            if([key isEqualToString:@"total_fuel"])
                total_fuel = [[dict objectForKey:key] intValue];
            if([key isEqualToString:@"startPoint"])
                startData = [dict objectForKey:key];
            if([key isEqualToString:@"endPoint"])
                endData = [dict objectForKey:key];
        }
        
        
        [self setup];
    }
    return self;
}
- (void)onEnterFrame:(SPEnterFrameEvent *)event
{
    if(self.x > spaceWidth / 2.0 * self.scaleX)
    {
        self.x = spaceWidth / 2.0 * self.scaleX;
    }else if(mGameWidth - self.x > spaceWidth / 2.0 * self.scaleX)
    {
        self.x = mGameWidth - spaceWidth / 2.0 * self.scaleX;
    }
    if(self.y > spaceHeight / 2.0 * self.scaleX)
    {
        self.y = spaceHeight / 2.0 * self.scaleX;
    }else if(mGameHeight - self.y > spaceHeight / 2.0 * self.scaleX)
    {
        self.y = mGameHeight - spaceHeight / 2.0 * self.scaleX;
    }
}




- (void)onTouchEvent:(SPTouchEvent*)event
{
    NSArray *touches = [[event touchesWithTarget:self andPhase:SPTouchPhaseMoved] allObjects];
    
    if (touches.count == 1)
    {
        // one finger touching -> move
        SPTouch *touch = [touches objectAtIndex:0];
        
        SPPoint *currentPos = [touch locationInSpace:self.parent];
        SPPoint *previousPos = [touch previousLocationInSpace:self.parent];
        SPPoint *dist = [currentPos subtractPoint:previousPos];
        self.x += dist.x;
        self.y += dist.y;
    }
    else if (touches.count >= 2)
    {
        // two fingers touching -> rotate and scale
        SPTouch *touch1 = [touches objectAtIndex:0];
        SPTouch *touch2 = [touches objectAtIndex:1];
        
        SPPoint *touch1PrevPos = [touch1 previousLocationInSpace:self.parent];
        SPPoint *touch1Pos = [touch1 locationInSpace:self.parent];
        SPPoint *touch2PrevPos = [touch2 previousLocationInSpace:self.parent];
        SPPoint *touch2Pos = [touch2 locationInSpace:self.parent];
        
        SPPoint *prevVector = [touch1PrevPos subtractPoint:touch2PrevPos];
        SPPoint *vector = [touch1Pos subtractPoint:touch2Pos];
        
        // update pivot point based on previous center
        //SPPoint *touch1PrevLocalPos = [touch1 previousLocationInSpace:self];
        //SPPoint *touch2PrevLocalPos = [touch2 previousLocationInSpace:self];
        //self.pivotX = (touch1PrevLocalPos.x + touch2PrevLocalPos.x) * 0.5f;
        //self.pivotY = (touch1PrevLocalPos.y + touch2PrevLocalPos.y) * 0.5f;
        
        // update location based on the current center
        //self.x = (touch1Pos.x + touch2Pos.x) * 0.5f;
        //self.y = (touch1Pos.y + touch2Pos.y) * 0.5f;
        
        //float angleDiff = vector.angle - prevVector.angle;
        //self.rotation += angleDiff;
        
        float sizeDiff = vector.length / prevVector.length;
        self.scaleX = self.scaleY = MIN(minZoom,MAX(maxZoom, self.scaleX * sizeDiff));
    }
    NSArray *touchesEnded = [[event touchesWithTarget:self andPhase:SPTouchPhaseEnded] allObjects];
    
    if (touchesEnded.count == 1)
    {
        // one finger touching -> move
        SPTouch *touch = [touchesEnded objectAtIndex:0];
        if(touch.tapCount > 1)
        {
            SPPoint *currentPos = [touch locationInSpace:self];
            if(self.scaleX != minZoom)
            {
                SPTween *tween = [SPTween tweenWithTarget:self time:0.5f transition:SP_TRANSITION_EASE_OUT];
                [tween moveToX:mGameWidth / 2.0 * minZoom + mGameWidth / 2.0 - currentPos.x * minZoom y:mGameHeight / 2.0 * minZoom + mGameHeight / 2.0 - currentPos.y * minZoom];
                [tween scaleTo:minZoom];
                [self.stage.juggler addObject:tween];
            }else{
                SPTween *tween = [SPTween tweenWithTarget:self time:0.5f transition:SP_TRANSITION_EASE_OUT];
                [tween moveToX:284 y:140];
                [tween scaleTo:maxZoom];
                [self.stage.juggler addObject:tween];
            }
        }
    }
    touches = [[event touchesWithTarget:self andPhase:SPTouchPhaseEnded] allObjects];
    if (touches.count == 1)
    {
        SPTouch *touch = [touches objectAtIndex:0];
        if (touch.tapCount == 2)
        {
            // bring self to front
            SPDisplayObjectContainer *parent = self.parent;
            [parent removeChild:self];
            [parent addChild:self];
        }
    }
}

- (void)dealloc
{
    [Media releaseAtlas];
    [Media releaseSound];
}

- (void)setup
{
    [Media initAtlas];
    [Media initSound];
    
    SPImage *background = [[SPImage alloc] initWithContentsOfFile:@"stars.jpg"];
    background.pivotX = background.width / 2;
    background.pivotY = background.height / 2;
    background.x = mGameWidth / 2;
    background.y = mGameHeight / 2;
    [self addChild:background];
    
    SPImage *egg = [[SPImage alloc] initWithTexture:[Media atlasTexture:@"planet_4"]];
    egg.pivotX = (int)egg.width / 2;
    egg.pivotY = (int)egg.height / 2;
    egg.x = mGameWidth / 2;
    egg.y = mGameHeight / 2 + 50;
    [self addChild:egg];
}


@end
