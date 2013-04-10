//
//  FuelUsedScatterPlot.m
//  Space Flight
//
//  Created by Michael Stratford on 4/9/13.
//  Copyright (c) 2013 JLOOP. All rights reserved.
//

#import "FuelUsedScatterPlot.h"

@implementation FuelUsedScatterPlot
@synthesize hostingView = _hostingView;
@synthesize graph = _graph;
@synthesize graphData = _graphData;

// Initialise the scatter plot in the provided hosting view with the provided data.
// The data array should contain NSValue objects each representing a CGPoint.
-(id)initWithHostingView:(CPTGraphHostingView *)hostingView andData:(NSMutableArray *)data
{
    self = [super init];
    
    if ( self != nil ) {
        self.hostingView = hostingView;
        self.graphData = data;
        self.graph = nil;
    }
    
    return self;
}

// This does the actual work of creating the plot if we don't already have a graph object.
-(void)initializePlotWithXMin:(float)xAxisMin xMax:(float)xAxisMax yMin:(float)yAxisMin yMax:(float)yAxisMax
{
    // Start with some simple sanity checks before we kick off
    if ( (self.hostingView == nil) || (self.graphData == nil) ) {
        NSLog(@"FuelUsedScatterPlot.h: Cannot initialise plot without hosting view or data.");
        return;
    }
    
    if ( self.graph != nil ) {
        NSLog(@"FuelUsedScatterPlot.h: Graph object already exists.");
        return;
    }
    
    // Create a graph object which we will use to host just one scatter plot.
    CGRect frame = [self.hostingView bounds];
    self.graph = [[[CPTXYGraph alloc] initWithFrame:frame] autorelease];
    
    // Add some padding to the graph, with more at the bottom for axis labels.
    self.graph.plotAreaFrame.paddingTop = 20.0f;
    self.graph.plotAreaFrame.paddingRight = 20.0f;
    self.graph.plotAreaFrame.paddingBottom = 57.0f;
    self.graph.plotAreaFrame.paddingLeft = 70.0f;
    CPTTheme *theme = [CPTTheme themeNamed:kCPTDarkGradientTheme];
    [self.graph applyTheme:theme];
    
    // Tie the graph we've created with the hosting view.
    self.hostingView.hostedGraph = self.graph;
    
    // If you want to use one of the default themes - apply that here.
    //[self.graph applyTheme:[CPTTheme themeNamed:kCPTDarkGradientTheme]];
    
    // Create a line style that we will apply to the axis and data line.
    
    
    CPTMutableLineStyle *lineStyle = [CPTMutableLineStyle lineStyle];
    lineStyle.lineWidth              = 2.0f;
    lineStyle.lineColor              = [CPTColor whiteColor];
    
    
    NSMutableArray *lineStyles = [[NSMutableArray alloc] init];
    
    CPTMutableLineStyle *lineStyle1 = [CPTMutableLineStyle lineStyle];
    lineStyle1.lineWidth              = 3.0f;
    lineStyle1.lineColor              = [CPTColor colorWithComponentRed:0.0 green:1.0 blue:0.0 alpha:1.0];
    //lineStyle1.dashPattern            = [NSArray arrayWithObjects:[NSNumber numberWithFloat:5.0f], [NSNumber numberWithFloat:5.0f], nil];
    [lineStyles addObject:lineStyle1];
    CPTMutableLineStyle *lineStyle2 = [CPTMutableLineStyle lineStyle];
    lineStyle2.lineWidth              = 2.5f;
    lineStyle2.lineColor              = [CPTColor colorWithComponentRed:0.0 green:0.7 blue:0.25 alpha:0.8];
    //lineStyle2.dashPattern            = [NSArray arrayWithObjects:[NSNumber numberWithFloat:5.0f], [NSNumber numberWithFloat:5.0f], nil];
    [lineStyles addObject:lineStyle2];
    CPTMutableLineStyle *lineStyle3 = [CPTMutableLineStyle lineStyle];
    lineStyle3.lineWidth              = 2.0f;
    lineStyle3.lineColor              = [CPTColor colorWithComponentRed:0.0 green:0.6 blue:0.25 alpha:0.6];
    //lineStyle3.dashPattern            = [NSArray arrayWithObjects:[NSNumber numberWithFloat:5.0f], [NSNumber numberWithFloat:5.0f], nil];
    [lineStyles addObject:lineStyle3];
    CPTMutableLineStyle *lineStyle4 = [CPTMutableLineStyle lineStyle];
    lineStyle4.lineWidth              = 1.5f;
    lineStyle4.lineColor              = [CPTColor colorWithComponentRed:0.0 green:0.5 blue:0.25 alpha:0.4];
    //lineStyle4.dashPattern            = [NSArray arrayWithObjects:[NSNumber numberWithFloat:5.0f], [NSNumber numberWithFloat:5.0f], nil];
    [lineStyles addObject:lineStyle4];
    CPTMutableLineStyle *lineStyle5 = [CPTMutableLineStyle lineStyle];
    lineStyle5.lineWidth              = 1.0f;
    lineStyle5.lineColor              = [CPTColor colorWithComponentRed:0.0 green:0.4 blue:0.25 alpha:0.2];
    //lineStyle5.dashPattern            = [NSArray arrayWithObjects:[NSNumber numberWithFloat:5.0f], [NSNumber numberWithFloat:5.0f], nil];
    [lineStyles addObject:lineStyle5];
    
    // Put an area gradient under the plot above
    //CPTColor *areaColor       = [CPTColor colorWithComponentRed:0.3 green:1.0 blue:0.3 alpha:0.8];
    //CPTGradient *areaGradient = [CPTGradient gradientWithBeginningColor:areaColor endingColor:[CPTColor clearColor]];
    //areaGradient.angle = -90.0f;
    //CPTFill *areaGradientFill = [CPTFill fillWithGradient:areaGradient];
    
    
    // Create a text style that we will use for the axis labels.
    CPTMutableTextStyle *textStyle = [CPTMutableTextStyle textStyle];
    textStyle.fontName = @"Helvetica";
    textStyle.fontSize = 14;
    textStyle.color = [CPTColor whiteColor];
    
    // Create the plot symbol we're going to use.
    //CPTPlotSymbol *plotSymbol = [CPTPlotSymbol crossPlotSymbol];
    //plotSymbol.lineStyle = lineStyle;
    //plotSymbol.size = CGSizeMake(8.0, 8.0);
    
    // Setup some floats that represent the min/max values on our axis.
    
    // We modify the graph's plot space to setup the axis' min / max values.
    CPTXYPlotSpace *plotSpace = (CPTXYPlotSpace *)self.graph.defaultPlotSpace;
    plotSpace.xRange = [CPTPlotRange plotRangeWithLocation:CPTDecimalFromFloat(xAxisMin) length:CPTDecimalFromFloat(xAxisMax - xAxisMin)];
    plotSpace.yRange = [CPTPlotRange plotRangeWithLocation:CPTDecimalFromFloat(yAxisMin) length:CPTDecimalFromFloat(yAxisMax - yAxisMin)];
    
    // Modify the graph's axis with a label, line style, etc.
    CPTXYAxisSet *axisSet = (CPTXYAxisSet *)self.graph.axisSet;
    
    axisSet.xAxis.title = @"Seconds";
    axisSet.xAxis.titleTextStyle = textStyle;
    axisSet.xAxis.titleOffset = 30.0f;
    axisSet.xAxis.axisLineStyle = lineStyle;
    axisSet.xAxis.majorTickLineStyle = lineStyle;
    axisSet.xAxis.minorTickLineStyle = lineStyle;
    axisSet.xAxis.labelTextStyle = textStyle;
    axisSet.xAxis.labelOffset = 3.0f;
    axisSet.xAxis.majorIntervalLength = CPTDecimalFromFloat(xAxisMax / 10.0);
    axisSet.xAxis.minorTicksPerInterval = 1;
    axisSet.xAxis.minorTickLength = 5.0f;
    axisSet.xAxis.majorTickLength = 7.0f;
    
    axisSet.yAxis.title = @"Fuel Used";
    axisSet.yAxis.titleTextStyle = textStyle;
    axisSet.yAxis.titleOffset = 40.0f;
    axisSet.yAxis.axisLineStyle = lineStyle;
    axisSet.yAxis.majorTickLineStyle = lineStyle;
    axisSet.yAxis.minorTickLineStyle = lineStyle;
    axisSet.yAxis.labelTextStyle = textStyle;
    axisSet.yAxis.labelOffset = 3.0f;
    axisSet.yAxis.majorIntervalLength = CPTDecimalFromFloat(yAxisMax / 10.0);
    axisSet.yAxis.minorTicksPerInterval = 1;
    axisSet.yAxis.minorTickLength = 5.0f;
    axisSet.yAxis.majorTickLength = 7.0f;
    
    // Add a plot to our graph and axis. We give it an identifier so that we
    // could add multiple plots (data lines) to the same graph if necessary.
    
    for (NSDictionary *dic in self.graphData) {
        CPTScatterPlot *plot = [[[CPTScatterPlot alloc] init] autorelease];
        plot.dataSource = self;
        plot.identifier = [dic objectForKey:@"PLOT_IDENTIFIER"];
        plot.dataLineStyle = [lineStyles objectAtIndex:([[dic objectForKey:@"PLOT_LINE_STYLE"] intValue] - 1)];
        //plot.interpolation = CPTScatterPlotInterpolationCurved;
        if(([[dic objectForKey:@"PLOT_LINE_STYLE"] intValue] - 1) == 0)
        {
            //plot.areaFill      = areaGradientFill;
            //plot.areaBaseValue = CPTDecimalFromString(@"1.75");
        }
        [self.graph addPlot:plot];
    }
}

// Delegate method that returns the number of points on the plot
-(NSUInteger)numberOfRecordsForPlot:(CPTPlot *)plot
{
    for (NSDictionary *dic in self.graphData) {
        NSString *identity = [dic objectForKey:@"PLOT_IDENTIFIER"];
        if([plot.identifier isEqual:identity]){
            NSArray *arr = [dic objectForKey:@"PLOT_DATA"];
            return [arr count];
        }
    }
    return 0;
}

// Delegate method that returns a single X or Y value for a given plot.
-(NSNumber *)numberForPlot:(CPTPlot *)plot field:(NSUInteger)fieldEnum recordIndex:(NSUInteger)index
{
    for (NSDictionary *dic in self.graphData) {
        NSString *identity = [dic objectForKey:@"PLOT_IDENTIFIER"];
        if([plot.identifier isEqual:identity]){
            NSArray *arr = [dic objectForKey:@"PLOT_DATA"];
            
            NSValue *value = [arr objectAtIndex:index];
            CGPoint point = [value CGPointValue];
            
            // FieldEnum determines if we return an X or Y value.
            if ( fieldEnum == CPTScatterPlotFieldX )
            {
                return [NSNumber numberWithFloat:point.x];
            }
            else    // Y-Axis
            {
                return [NSNumber numberWithFloat:point.y];
            }
            
            
        }
    }
    
    return [NSNumber numberWithFloat:0];
}

@end
