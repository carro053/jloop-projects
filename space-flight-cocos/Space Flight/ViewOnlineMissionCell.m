//
//  ViewOnlineMissionCell.m
//  Space Flight
//
//  Created by Michael Stratford on 7/4/12.
//  Copyright (c) 2012 JLOOP. All rights reserved.
//

#import "ViewOnlineMissionCell.h"

@implementation ViewOnlineMissionCell
@synthesize textLabel;
@synthesize viewButton;
@synthesize tweetButton;

- (id)initWithStyle:(UITableViewCellStyle)style reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {
        // Initialization code
    }
    return self;
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated
{
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}

- (void)dealloc {
    [textLabel release];
    [viewButton release];
    [tweetButton release];
    [super dealloc];
}
@end
